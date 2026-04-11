<?php

namespace Weboldalnet\TimeBooking\Http\Controllers\Site\TimeBooking;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Weboldalnet\TimeBooking\Models\Category;
use Weboldalnet\TimeBooking\Models\Appointment;
use Weboldalnet\TimeBooking\Models\Booking;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    /**
     * Display the booking page with categories and appointments.
     */
    public function index(): View
    {
        $categories = Category::active()
            ->ordered()
            ->with(['activeAppointments' => function ($query) {
                $query->future()->available()->ordered();
            }])
            ->get();

        return view('timebooking::site.elements.timebooking.index', compact('categories'));
    }

    /**
     * Show the booking form for a specific appointment.
     */
    public function show(Appointment $appointment)
    {
        // Check if appointment is available for booking
        if (!$appointment->is_active || $appointment->is_fully_booked || $appointment->start_time <= now()) {
            return response()->json([
                'success' => false,
                'message' => 'Ez az időpont már nem elérhető foglalásra.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'appointment' => [
                'id' => $appointment->id,
                'title' => $appointment->title,
                'description' => $appointment->description,
                'start_time' => $appointment->start_time->format('Y-m-d H:i'),
                'end_time' => $appointment->end_time->format('Y-m-d H:i'),
                'available_capacity' => $appointment->available_capacity,
                'category' => $appointment->category->name,
            ]
        ]);
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'appointment_id' => 'required|exists:timebooking_appointments,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'nullable|string|max:1000',
        ]);

        $appointment = Appointment::findOrFail($validated['appointment_id']);

        // Check if appointment is still available
        if (!$appointment->is_active || $appointment->is_fully_booked || $appointment->start_time <= now()) {
            return redirect()->back()
                ->with('error', 'Ez az időpont már nem elérhető foglalásra.')
                ->withInput();
        }

        // Create the booking
        $booking = Booking::create([
            'appointment_id' => $appointment->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
            'status' => Booking::STATUS_CONFIRMED, // Auto-confirm bookings
            'booking_date' => now(),
        ]);

        // Decrease appointment capacity
        $appointment->decreaseCapacity();

        // Redirect to thank you page
        return redirect()->route('site.timebooking.thank-you', ['booking' => $booking->id])
            ->with('success', 'Foglalása sikeresen rögzítve!');
    }

    /**
     * Show the thank you page after successful booking.
     */
    public function thankYou(Booking $booking): View
    {
        $booking->load('appointment.category');
        return view('timebooking::site.elements.timebooking.thank-you', compact('booking'));
    }

    /**
     * Get available appointments for a specific category (AJAX).
     */
    public function getAppointments(Category $category)
    {
        $appointments = $category->activeAppointments()
            ->future()
            ->available()
            ->ordered()
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->title,
                    'description' => $appointment->description,
                    'start_time' => $appointment->start_time->format('Y-m-d H:i'),
                    'end_time' => $appointment->end_time->format('Y-m-d H:i'),
                    'available_capacity' => $appointment->available_capacity,
                    'formatted_date' => $appointment->start_time->format('Y. m. d.'),
                    'formatted_time' => $appointment->start_time->format('H:i') . ' - ' . $appointment->end_time->format('H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'appointments' => $appointments
        ]);
    }

    /**
     * Check appointment availability (AJAX).
     */
    public function checkAvailability(Appointment $appointment)
    {
        $isAvailable = $appointment->is_active && 
                      !$appointment->is_fully_booked && 
                      $appointment->start_time > now();

        return response()->json([
            'success' => true,
            'available' => $isAvailable,
            'available_capacity' => $appointment->available_capacity,
            'message' => $isAvailable ? 'Időpont elérhető' : 'Időpont már nem elérhető'
        ]);
    }
}
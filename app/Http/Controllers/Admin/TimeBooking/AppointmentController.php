<?php

namespace Weboldalnet\TimeBooking\Http\Controllers\Admin\TimeBooking;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Weboldalnet\TimeBooking\Models\Appointment;
use Weboldalnet\TimeBooking\Models\Category;
use App\Http\Controllers\Controller;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index(Request $request): View
    {
        $query = Appointment::with(['category', 'bookings']);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('start_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('start_time', '<=', $request->date_to);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'booked') {
                $query->whereHas('bookings');
            } elseif ($request->status === 'available') {
                $query->where('available_capacity', '>', 0);
            }
        }

        $appointments = $query->ordered()->paginate(20);
        $categories = Category::active()->ordered()->get();

        return view('timebooking::admin.timebooking.appointments.index', compact('appointments', 'categories'));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create(): View
    {
        $categories = Category::active()->ordered()->get();
        return view('timebooking::admin.timebooking.appointments.create', compact('categories'));
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:timebooking_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'capacity' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['available_capacity'] = $validated['capacity'];
        $validated['is_active'] = $request->has('is_active');

        Appointment::create($validated);

        return redirect()->route('admin.timebooking.appointments.index')
            ->with('success', 'Időpont sikeresen létrehozva.');
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment): View
    {
        $appointment->load(['category', 'bookings.appointment']);
        return view('timebooking::admin.timebooking.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment): View
    {
        // Only allow editing if no bookings exist
        if ($appointment->has_bookings) {
            return redirect()->route('admin.timebooking.appointments.index')
                ->with('error', 'Nem szerkeszthető olyan időpont, amelyre már van foglalás.');
        }

        $categories = Category::active()->ordered()->get();
        return view('timebooking::admin.timebooking.appointments.edit', compact('appointment', 'categories'));
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        // Only allow updating if no bookings exist
        if ($appointment->has_bookings) {
            return redirect()->route('admin.timebooking.appointments.index')
                ->with('error', 'Nem módosítható olyan időpont, amelyre már van foglalás.');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:timebooking_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'capacity' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['available_capacity'] = $validated['capacity'];
        $validated['is_active'] = $request->has('is_active');

        $appointment->update($validated);

        return redirect()->route('admin.timebooking.appointments.index')
            ->with('success', 'Időpont sikeresen frissítve.');
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Appointment $appointment): RedirectResponse
    {
        // Only allow deletion if no bookings exist
        if ($appointment->has_bookings) {
            return redirect()->route('admin.timebooking.appointments.index')
                ->with('error', 'Nem törölhető olyan időpont, amelyre már van foglalás.');
        }

        $appointment->delete();

        return redirect()->route('admin.timebooking.appointments.index')
            ->with('success', 'Időpont sikeresen törölve.');
    }

    /**
     * Show bookings for a specific appointment in modal.
     */
    public function bookings(Appointment $appointment)
    {
        $bookings = $appointment->bookings()->ordered()->get();
        return response()->json([
            'appointment' => $appointment,
            'bookings' => $bookings,
        ]);
    }

    /**
     * Show the form for creating multiple appointments.
     */
    public function createBulk(): View
    {
        $categories = Category::active()->ordered()->get();
        return view('timebooking::admin.timebooking.appointments.create-bulk', compact('categories'));
    }

    /**
     * Store multiple appointments in storage.
     */
    public function storeBulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:timebooking_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'capacity' => 'required|integer|min:1|max:100',
            'days' => 'required|array|min:1',
            'days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'is_active' => 'boolean',
        ]);

        $dayMap = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 0,
        ];

        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $selectedDays = array_map(fn($day) => $dayMap[$day], $validated['days']);

        $appointments = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            if (in_array($current->dayOfWeek, $selectedDays)) {
                $startDateTime = $current->copy()->setTimeFromTimeString($validated['start_time']);
                $endDateTime = $current->copy()->setTimeFromTimeString($validated['end_time']);

                $appointments[] = [
                    'category_id' => $validated['category_id'],
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'start_time' => $startDateTime,
                    'end_time' => $endDateTime,
                    'capacity' => $validated['capacity'],
                    'available_capacity' => $validated['capacity'],
                    'is_active' => $request->has('is_active'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $current->addDay();
        }

        Appointment::insert($appointments);

        return redirect()->route('admin.timebooking.appointments.index')
            ->with('success', count($appointments) . ' időpont sikeresen létrehozva.');
    }
}
<?php

namespace Weboldalnet\TimeBooking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Weboldalnet\TimeBooking\Models\Booking
 *
 * @property int $id
 * @property int $appointment_id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $message
 * @property string $status
 * @property \Illuminate\Support\Carbon $booking_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Weboldalnet\TimeBooking\Models\Appointment $appointment
 * @property-read bool $is_confirmed
 * @property-read bool $is_pending
 * @property-read bool $is_cancelled
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereAppointmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereBookingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUpdatedAt($value)
 */
class Booking extends Model
{
    use HasFactory;

    protected $table = 'timebooking_bookings';

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'appointment_id',
        'name',
        'email',
        'phone',
        'message',
        'status',
        'booking_date',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
    ];

    /**
     * Get the appointment that owns the booking.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Check if the booking is confirmed.
     */
    public function getIsConfirmedAttribute(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Check if the booking is pending.
     */
    public function getIsPendingAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the booking is cancelled.
     */
    public function getIsCancelledAttribute(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Scope a query to only include confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope a query to only include pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include cancelled bookings.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope a query to order by booking date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('booking_date', 'desc');
    }

    /**
     * Confirm the booking and decrease appointment capacity.
     */
    public function confirm(): bool
    {
        if ($this->status === self::STATUS_PENDING) {
            $this->status = self::STATUS_CONFIRMED;
            $this->save();
            
            // Decrease appointment capacity
            $this->appointment->decreaseCapacity();
            
            return true;
        }
        return false;
    }

    /**
     * Cancel the booking and increase appointment capacity if it was confirmed.
     */
    public function cancel(): bool
    {
        $wasConfirmed = $this->status === self::STATUS_CONFIRMED;
        
        $this->status = self::STATUS_CANCELLED;
        $this->save();
        
        // Increase appointment capacity if booking was confirmed
        if ($wasConfirmed) {
            $this->appointment->increaseCapacity();
        }
        
        return true;
    }
}
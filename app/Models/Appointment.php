<?php

namespace Weboldalnet\TimeBooking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Weboldalnet\TimeBooking\Models\Appointment
 *
 * @property int $id
 * @property int $category_id
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $start_time
 * @property \Illuminate\Support\Carbon $end_time
 * @property int $capacity
 * @property int $available_capacity
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Weboldalnet\TimeBooking\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Weboldalnet\TimeBooking\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read bool $is_fully_booked
 * @property-read bool $has_bookings
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereAvailableCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereUpdatedAt($value)
 */
class Appointment extends Model
{
    use HasFactory;

    protected $table = 'timebooking_appointments';

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'capacity',
        'available_capacity',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'capacity' => 'integer',
        'available_capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category that owns the appointment.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the bookings for the appointment.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get confirmed bookings for the appointment.
     */
    public function confirmedBookings(): HasMany
    {
        return $this->hasMany(Booking::class)->where('status', 'confirmed');
    }

    /**
     * Check if the appointment is fully booked.
     */
    public function getIsFullyBookedAttribute(): bool
    {
        return $this->available_capacity <= 0;
    }

    /**
     * Check if the appointment has any bookings.
     */
    public function getHasBookingsAttribute(): bool
    {
        return $this->bookings()->count() > 0;
    }

    /**
     * Scope a query to only include active appointments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include available appointments.
     */
    public function scopeAvailable($query)
    {
        return $query->where('available_capacity', '>', 0);
    }

    /**
     * Scope a query to only include future appointments.
     */
    public function scopeFuture($query)
    {
        return $query->where('start_time', '>', now());
    }

    /**
     * Scope a query to order by start time.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('start_time');
    }

    /**
     * Decrease available capacity when a booking is made.
     */
    public function decreaseCapacity(): bool
    {
        if ($this->available_capacity > 0) {
            $this->decrement('available_capacity');
            return true;
        }
        return false;
    }

    /**
     * Increase available capacity when a booking is cancelled.
     */
    public function increaseCapacity(): bool
    {
        if ($this->available_capacity < $this->capacity) {
            $this->increment('available_capacity');
            return true;
        }
        return false;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\BarbershopEvent
 *
 * @property int $id
 * @property int $barbershop_id
 * @property string $name
 * @property int $days_duration_slots
 * @property int $max_clients_per_slot
 * @property int $minutes_every_slots
 * @property int $minutes_break_between_slots
 * @property mixed $schedule_settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|BarbershopAppointment[] $appointments
 * @property-read int|null $appointments_count
 */
class BarbershopEvent extends Model
{
    use HasFactory;

    public const SETTING_DAYS = 'days';
    public const SETTING_WORKDAY = 'workday';
    public const SETTING_START_TIME = 'start_time';
    public const SETTING_END_TIME = 'end_time';
    public const SETTING_BREAKS = 'breaks';
    public const SETTING_HOLIDAYS = 'holidays';

    public function appointments(): HasMany
    {
        return $this->hasMany(BarbershopAppointment::class, 'barbershop_event_id');
    }

    protected $casts = [
        'schedule_settings' => 'array',
    ];
}

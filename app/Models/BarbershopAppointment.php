<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BarbershopAppointment
 *
 * @property int $id
 * @property int $barbershop_event_id
 * @property string $datetime
 * @property string $email
 * @property string $firstname
 * @property string $lastname
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class BarbershopAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'barbershop_event_id',
        'datetime',
        'email',
        'firstname',
        'lastname',
    ];
}

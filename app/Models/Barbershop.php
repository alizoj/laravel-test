<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Barbershop
 *
 * @property integer $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|BarbershopEvent[] $events
 * @property-read int|null $events_count
 */
class Barbershop extends Model
{
    use HasFactory;

    public function events(): HasMany
    {
        return $this->hasMany(BarbershopEvent::class, 'barbershop_id');
    }
}

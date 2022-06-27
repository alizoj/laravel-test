<?php

namespace App\Observers;

use Exception;
use Carbon\Carbon;
use Psr\SimpleCache\InvalidArgumentException;
use App\Models\BarbershopAppointment;

class BarbershopAppointmentObserver
{
    /**
     * Handle the BarbershopAppointment "created" event.
     *
     * @param BarbershopAppointment $barbershopAppointment
     * @return void
     * @throws Exception|InvalidArgumentException
     */
    public function created(BarbershopAppointment $barbershopAppointment): void
    {
        cache()->delete('slots_'
            . $barbershopAppointment->barbershop_event_id
            . '_'
            . Carbon::createFromFormat('Y-m-d H:i:s', $barbershopAppointment->datetime)
                ->format('Y-m-d')
        );
    }

    /**
     * Handle the BarbershopAppointment "updated" event.
     *
     * @param BarbershopAppointment $barbershopAppointment
     * @return void
     */
    public function updated(BarbershopAppointment $barbershopAppointment): void
    {
        //
    }

    /**
     * Handle the BarbershopAppointment "deleted" event.
     *
     * @param BarbershopAppointment $barbershopAppointment
     * @return void
     */
    public function deleted(BarbershopAppointment $barbershopAppointment): void
    {
        //
    }

    /**
     * Handle the BarbershopAppointment "restored" event.
     *
     * @param BarbershopAppointment $barbershopAppointment
     * @return void
     */
    public function restored(BarbershopAppointment $barbershopAppointment): void
    {
        //
    }

    /**
     * Handle the BarbershopAppointment "force deleted" event.
     *
     * @param BarbershopAppointment $barbershopAppointment
     * @return void
     */
    public function forceDeleted(BarbershopAppointment $barbershopAppointment): void
    {
        //
    }
}

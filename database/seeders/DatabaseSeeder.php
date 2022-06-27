<?php

namespace Database\Seeders;

use App\Models\Barbershop;
use App\Models\BarbershopEvent;
use App\Models\MenuItem;
use App\Models\Workshop;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    protected function seedMenu(): void
    {
        $rootItem = MenuItem::create([
            'name' => 'All events',
            'url' => '/events',
        ]);

        $laraconItem = MenuItem::create([
            'name' => 'Laracon',
            'url' => '/events/laracon',
            'parent_id' => $rootItem->id
        ]);

        MenuItem::create([
            'name' => 'Illuminate your knowledge of the laravel code base',
            'url' => '/events/laracon/workshops/illuminate',
            'parent_id' => $laraconItem->id
        ]);

        MenuItem::create([
            'name' => 'The new Eloquent - load more with less',
            'url' => '/events/laracon/workshops/eloquent',
            'parent_id' => $laraconItem->id
        ]);

        $reactconItem = MenuItem::create([
            'name' => 'Reactcon',
            'url' => '/events/reactcon',
            'parent_id' => $rootItem->id
        ]);

        MenuItem::create([
            'name' => '#NoClass pure functional programming',
            'url' => '/events/reactcon/workshops/noclass',
            'parent_id' => $reactconItem->id
        ]);

        MenuItem::create([
            'name' => 'Navigating the function jungle',
            'url' => '/events/reactcon/workshops/jungle',
            'parent_id' => $reactconItem->id
        ]);
    }

    protected function seedEvents(): void
    {
        $date = (new Carbon())->subYear()->setDay(21);

        $lcon1 = Event::create([
            'name' => 'Laravel convention ' . $date->year
        ]);

        Workshop::create([
            'start' => $date->clone()->setMonth(2)->setHour(10),
            'end' => $date->clone()->setMonth(2)->setHour(16),
            'name' => 'Illuminate your knowledge of the laravel code base',
            'event_id' => $lcon1->id
        ]);

        $date = (new Carbon())->addYears(1);

        $lcon2 = Event::create([
            'name' => 'Laravel convention ' . $date->year
        ]);

        Workshop::create([
            'start' => $date->clone()->setMonth(10)->setHour(10),
            'end' => $date->clone()->setMonth(10)->setHour(16),
            'name' => 'The new Eloquent - load more with less',
            'event_id' => $lcon2->id
        ]);

        Workshop::create([
            'start' => $date->clone()->setMonth(11)->setHour(10),
            'end' => $date->clone()->setMonth(11)->setHour(17),
            'name' => 'AutoEx - handles exceptions 100% automatic',
            'event_id' => $lcon2->id
        ]);

        $rcon = Event::create([
            'name' => 'React convention ' . $date->year
        ]);

        Workshop::create([
            'start' => $date->clone()->setMonth(8)->setHour(10),
            'end' => $date->clone()->setMonth(8)->setHour(18),
            'name' => '#NoClass pure functional programming',
            'event_id' => $rcon->id
        ]);

        Workshop::create([
            'start' => $date->clone()->setMonth(11)->setHour(9),
            'end' => $date->clone()->setMonth(11)->setHour(17),
            'name' => 'Navigating the function jungle',
            'event_id' => $rcon->id
        ]);
    }

    protected function seedBarbershop(): void
    {
        $barbershop = Barbershop::create([
            'name' => 'Barbershop One'
        ]);

        $schedule_settings = [
            BarbershopEvent::SETTING_DAYS => [
                0 => [
                    BarbershopEvent::SETTING_WORKDAY => false,
                    BarbershopEvent::SETTING_START_TIME => null,
                    BarbershopEvent::SETTING_END_TIME => null,
                ],
                1 => [
                    BarbershopEvent::SETTING_WORKDAY => true,
                    BarbershopEvent::SETTING_START_TIME => '08:00',
                    BarbershopEvent::SETTING_END_TIME => '20:00',
                ],
                2 => [
                    BarbershopEvent::SETTING_WORKDAY => true,
                    BarbershopEvent::SETTING_START_TIME => '08:00',
                    BarbershopEvent::SETTING_END_TIME => '20:00',
                ],
                3 => [
                    BarbershopEvent::SETTING_WORKDAY => true,
                    BarbershopEvent::SETTING_START_TIME => '08:00',
                    BarbershopEvent::SETTING_END_TIME => '20:00',
                ],
                4 => [
                    BarbershopEvent::SETTING_WORKDAY => true,
                    BarbershopEvent::SETTING_START_TIME => '08:00',
                    BarbershopEvent::SETTING_END_TIME => '20:00',
                ],
                5 => [
                    BarbershopEvent::SETTING_WORKDAY => true,
                    BarbershopEvent::SETTING_START_TIME => '08:00',
                    BarbershopEvent::SETTING_END_TIME => '20:00',
                ],
                6 => [
                    BarbershopEvent::SETTING_WORKDAY => true,
                    BarbershopEvent::SETTING_START_TIME => '10:00',
                    BarbershopEvent::SETTING_END_TIME => '22:00',
                ],
            ],
            BarbershopEvent::SETTING_BREAKS => [
                'lunch break' => [
                    BarbershopEvent::SETTING_START_TIME => '12:00',
                    BarbershopEvent::SETTING_END_TIME => '13:00'
                ],
                'cleaning break' => [
                    BarbershopEvent::SETTING_START_TIME => '15:00',
                    BarbershopEvent::SETTING_END_TIME => '16:00'
                ],
            ],
            BarbershopEvent::SETTING_HOLIDAYS => [
                '2020-06-29' => true,
                '2020-07-02' => true,
            ]
        ];

        /**
         * Men Haircut
         *   slots for the next 7 days, sunday off
         *   from 08:00-20:00 monday to friday
         *   from 10:00-22:00 saturday
         *   lunch break at 12:00-13:00
         *   cleaning break at 15:00-16:00
         *   max 3 clients per slot
         *   slots every 10 minutes
         *   5 minutes cleanup break between slots
         *   the third day starting from now is a public holiday
         */
        BarbershopEvent::create([
            'barbershop_id' => $barbershop->id,
            'name' => 'Men Haircut',
            'days_duration_slots' => 7,
            'max_clients_per_slot' => 3,
            'minutes_every_slots' => 10,
            'minutes_break_between_slots' => 5,
            'schedule_settings' => $schedule_settings,
        ]);
        /**
         * Woman Haircut
         *   slots for the next 7 days, sunday off
         *   lunch break at 12:00-13:00
         *   from 08:00-20:00 monday to friday
         *   from 10:00-22:00 saturday
         *   cleaning break at 15:00-16:00
         *   slots every 1 hour
         *   10 minutes cleanup break
         *   max 3 clients per slot
         *   the third day starting from now is a public holiday
         */
        BarbershopEvent::create([
            'barbershop_id' => $barbershop->id,
            'name' => 'Woman Haircut',
            'days_duration_slots' => 7,
            'max_clients_per_slot' => 3,
            'minutes_every_slots' => 60,
            'minutes_break_between_slots' => 10,
            'schedule_settings' => $schedule_settings,
        ]);
    }

    public function run(): void
    {
        DB::transaction(function ($table) {
            $this->seedEvents();
            $this->seedMenu();
            $this->seedBarbershop();
        });
    }
}

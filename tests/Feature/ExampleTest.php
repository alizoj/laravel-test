<?php

namespace Tests\Feature;

use App\Models\BarbershopEvent;
use Faker\Provider\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;
    private array $schedule_settings = [
        BarbershopEvent::SETTING_DAYS => [
            0 => [
                BarbershopEvent::SETTING_WORKDAY => false,
                BarbershopEvent::SETTING_END_TIME => null,
                BarbershopEvent::SETTING_START_TIME => null,
            ],
            1 => [
                BarbershopEvent::SETTING_WORKDAY => true,
                BarbershopEvent::SETTING_END_TIME => '20:00',
                BarbershopEvent::SETTING_START_TIME => '08:00',
            ],
            2 => [
                BarbershopEvent::SETTING_WORKDAY => true,
                BarbershopEvent::SETTING_END_TIME => '20:00',
                BarbershopEvent::SETTING_START_TIME => '08:00',
            ],
            3 => [
                BarbershopEvent::SETTING_WORKDAY => true,
                BarbershopEvent::SETTING_END_TIME => '20:00',
                BarbershopEvent::SETTING_START_TIME => '08:00',
            ],
            4 => [
                BarbershopEvent::SETTING_WORKDAY => true,
                BarbershopEvent::SETTING_END_TIME => '20:00',
                BarbershopEvent::SETTING_START_TIME => '08:00',
            ],
            5 => [
                BarbershopEvent::SETTING_WORKDAY => true,
                BarbershopEvent::SETTING_END_TIME => '20:00',
                BarbershopEvent::SETTING_START_TIME => '08:00',
            ],
            6 => [
                BarbershopEvent::SETTING_WORKDAY => true,
                BarbershopEvent::SETTING_END_TIME => '22:00',
                BarbershopEvent::SETTING_START_TIME => '10:00',
            ],
        ],
        BarbershopEvent::SETTING_BREAKS => [
            'lunch break' => [
                BarbershopEvent::SETTING_END_TIME => '13:00',
                BarbershopEvent::SETTING_START_TIME => '12:00',
            ],
            'cleaning break' => [
                BarbershopEvent::SETTING_END_TIME => '16:00',
                BarbershopEvent::SETTING_START_TIME => '15:00',
            ],
        ],
        BarbershopEvent::SETTING_HOLIDAYS => [
            '2020-06-29' => true,
            '2020-07-02' => true,
        ]
    ];

    public function testWarmupEvents()
    {
        $datePast = (new Carbon())->subYear()->setDay(21);
        $dateFuture = (new Carbon())->addYears(1);

        $response = $this->get('/warmupevents');
        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonPath('0.name', 'Laravel convention ' . $datePast->year)
            ->assertJsonPath('1.name', 'Laravel convention ' . $dateFuture->year)
            ->assertJsonPath('2.name', 'React convention ' . $dateFuture->year);
    }

    public function testEvents()
    {
        $datePast = (new Carbon())->subYear()->setDay(21);
        $dateFuture = (new Carbon())->addYears(1);

        $response = $this->get('/events');
        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonPath('0.name', 'Laravel convention ' . $datePast->year)
            ->assertJsonPath('0.workshops.0.name', 'Illuminate your knowledge of the laravel code base')
            ->assertJsonPath('1.name', 'Laravel convention ' . $dateFuture->year)
            ->assertJsonPath('1.workshops.0.name', 'The new Eloquent - load more with less')
            ->assertJsonPath('1.workshops.1.name', 'AutoEx - handles exceptions 100% automatic')
            ->assertJsonPath('2.name', 'React convention ' . $dateFuture->year)
            ->assertJsonPath('2.workshops.0.name', '#NoClass pure functional programming')
            ->assertJsonPath('2.workshops.1.name', 'Navigating the function jungle');
    }

    public function testFutureEvents()
    {
        $dateFuture = (new Carbon())->addYears(1);

        $response = $this->get('/futureevents');
        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonPath('0.name', 'Laravel convention ' . $dateFuture->year)
            ->assertJsonPath('0.workshops.0.name', 'The new Eloquent - load more with less')
            ->assertJsonPath('0.workshops.1.name', 'AutoEx - handles exceptions 100% automatic')
            ->assertJsonPath('1.name', 'React convention ' . $dateFuture->year)
            ->assertJsonPath('1.workshops.0.name', '#NoClass pure functional programming')
            ->assertJsonPath('1.workshops.1.name', 'Navigating the function jungle');
    }

    public function testMenu()
    {
        $response = $this->get('/menu');
        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.children.0.name', 'Laracon')
            ->assertJsonPath('0.children.0.children.0.url', '/events/laracon/workshops/illuminate')
            ->assertJsonPath('0.children.0.children.1.url', '/events/laracon/workshops/eloquent')
            ->assertJsonPath('0.children.1.name', 'Reactcon')
            ->assertJsonPath('0.children.1.children.0.url', '/events/reactcon/workshops/noclass')
            ->assertJsonPath('0.children.1.children.1.url', '/events/reactcon/workshops/jungle');
    }

    public function testBarbershops()
    {
        $response = $this->getJson('/api/barbershops');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Barbershop One')
            ->assertJsonPath('data.0.events.0.id', 1)
            ->assertJsonPath('data.0.events.0.barbershop_id', 1)
            ->assertJsonPath('data.0.events.0.name', 'Men Haircut')
            ->assertJsonPath('data.0.events.0.days_duration_slots', 7)
            ->assertJsonPath('data.0.events.0.max_clients_per_slot', 3)
            ->assertJsonPath('data.0.events.0.minutes_every_slots', 10)
            ->assertJsonPath('data.0.events.0.minutes_break_between_slots', 5)
            ->assertJsonPath('data.0.events.0.schedule_settings', $this->schedule_settings)
            ->assertJsonPath('data.0.events.1.id', 2)
            ->assertJsonPath('data.0.events.1.barbershop_id', 1)
            ->assertJsonPath('data.0.events.1.name', 'Woman Haircut')
            ->assertJsonPath('data.0.events.1.days_duration_slots', 7)
            ->assertJsonPath('data.0.events.1.max_clients_per_slot', 3)
            ->assertJsonPath('data.0.events.1.minutes_every_slots', 60)
            ->assertJsonPath('data.0.events.1.minutes_break_between_slots', 10)
            ->assertJsonPath('data.0.events.1.schedule_settings', $this->schedule_settings);
    }

    public function testBarbershopSlots(): void
    {
        $this->travel(-1)->days(); //yesterday (out of range)
        $response = $this->getJson(
            sprintf('/api/getslots?id=%s&dateTime=%s', 1, now()->format('Y-m-d'))
        );
        $response->assertStatus(404);

        $events = BarbershopEvent::all();
        foreach ($events as $event) {
            $this->travelTo($event->created_at);
            $totalDays = $event->days_duration_slots;

            for ($i = 0; $i < $totalDays; $i++) {
                $this->travel($i)->days();
                $dayData = $event->schedule_settings[BarbershopEvent::SETTING_DAYS][now()->dayOfWeek];
                $response = $this->getJson(
                    sprintf('/api/getslots?id=%s&dateTime=%s', $event->id, now()->format('Y-m-d'))
                );

                if ($this->isWorkDay(now(), $event, $event->schedule_settings) !== true) {
                    //check invalid tests
                    $response->assertStatus(404);
                    $bookResponse = $this->postJson('/api/book', [
                        "id" => $event->id,
                        "dateTime" => now()->format('Y-m-d')
                            . ' '
                            . $dayData[BarbershopEvent::SETTING_START_TIME]
                            . ':00',
                        "email" => Str::random(10) . '@gmail.com',
                        "firstName" => Person::firstNameMale(),
                        "lastName" => Person::firstNameFemale()
                    ]);
                    $bookResponse->assertStatus(404); //can't book
                } else {
                    //check valid tests
                    $response->assertStatus(200)
                        ->assertJsonPath(
                            'data.' . $dayData[BarbershopEvent::SETTING_START_TIME],
                            [
                                'isAvailable' => true,
                                'available_slots' => $event->max_clients_per_slot
                            ]
                        )
                        ->assertJsonPath(
                            'data.' .
                            Carbon::createFromFormat('H:i', $dayData[BarbershopEvent::SETTING_START_TIME])
                                ->addMinutes($event->minutes_every_slots + $event->minutes_break_between_slots)
                                ->format('H:i'),
                            [
                                'isAvailable' => true,
                                'available_slots' => $event->max_clients_per_slot
                            ]
                        )
                        // invalid ex. 09:05
                        ->assertJsonMissingExact([
                            'data' =>
                                Carbon::createFromFormat('H:i', $dayData[BarbershopEvent::SETTING_START_TIME])
                                    ->addMinutes($event->minutes_every_slots)->format('H:i')

                        ])
                        //last time ex. 20:00 edge case
                        ->assertJsonMissingExact([
                            'data' => $dayData[BarbershopEvent::SETTING_END_TIME]

                        ]);

                    // lets book
                    for ($slot = $event->max_clients_per_slot; $slot > 0; $slot--) {
                        $response = $this->getJson(
                            sprintf('/api/getslots?id=%s&dateTime=%s', $event->id, now()->format('Y-m-d'))
                        );
                        $response->assertStatus(200)
                            ->assertJsonPath(
                                'data.' . $dayData[BarbershopEvent::SETTING_START_TIME],
                                [
                                    'isAvailable' => true,
                                    'available_slots' => $slot
                                ]
                            );

                        $bookResponse = $this->postJson('/api/book', [
                            "id" => $event->id,
                            "dateTime" => now()->format('Y-m-d')
                                . ' '
                                . $dayData[BarbershopEvent::SETTING_START_TIME]
                                . ':00',
                            "email" => Str::random(10) . '@gmail.com',
                            "firstName" => Person::firstNameMale(),
                            "lastName" => Person::firstNameFemale()
                        ]);
                        $bookResponse->assertStatus(200); //can book
                    }

                    $response = $this->getJson(
                        sprintf('/api/getslots?id=%s&dateTime=%s', $event->id, now()->format('Y-m-d'))
                    );
                    $response->assertStatus(200)
                        ->assertJsonPath(
                            'data.' . $dayData[BarbershopEvent::SETTING_START_TIME],
                            [
                                'isAvailable' => false,
                                'available_slots' => 0
                            ]
                        );

                    $bookResponse = $this->postJson('/api/book', [
                        "id" => $event->id,
                        "dateTime" => now()->format('Y-m-d')
                            . ' '
                            . $dayData[BarbershopEvent::SETTING_START_TIME]
                            . ':00',
                        "email" => Str::random(10) . '@gmail.com',
                        "firstName" => Person::firstNameMale(),
                        "lastName" => Person::firstNameFemale()
                    ]);
                    $bookResponse->assertStatus(404); //can't book because 3 times did
                }
            }
        }
    }

    private function isWorkDay(Carbon $lookingDay, BarbershopEvent $event, array $settings): bool
    {
        $diffDays = $event->created_at->diffInDays($lookingDay->format('Y-m-d H:i:s')) + 1;
        if ($diffDays > $event->days_duration_slots
            || now()->diffInDays($lookingDay, false) < 0) {
            return false;
        }

        if ($settings[$event::SETTING_DAYS][$lookingDay->dayOfWeek][$event::SETTING_WORKDAY] !== true
            || Arr::has($settings[$event::SETTING_HOLIDAYS], $lookingDay->format('Y-m-d'))) {
            return false;
        }

        return true;
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ExampleTest extends TestCase
{
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
        $schedule_settings = [
            'days' => [
                0 => [
                    'workday' => false,
                    'end_time' => null,
                    'start_time' => null,
                ],
                1 => [
                    'workday' => true,
                    'end_time' => '20:00',
                    'start_time' => '08:00',
                ],
                2 => [
                    'workday' => true,
                    'end_time' => '20:00',
                    'start_time' => '08:00',
                ],
                3 => [
                    'workday' => true,
                    'end_time' => '20:00',
                    'start_time' => '08:00',
                ],
                4 => [
                    'workday' => true,
                    'end_time' => '20:00',
                    'start_time' => '08:00',
                ],
                5 => [
                    'workday' => true,
                    'end_time' => '20:00',
                    'start_time' => '08:00',
                ],
                6 => [
                    'workday' => true,
                    'end_time' => '22:00',
                    'start_time' => '10:00',
                ],
            ],
            'breaks' => [
                'lunch break' => [
                    'end_time' => '13:00',
                    'start_time' => '12:00'
                ],
                'cleaning break' => [
                    'end_time' => '16:00',
                    'start_time' => '15:00'
                ],
            ]
        ];

        $response = $this->get('/barbershops');
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
            ->assertJsonPath('data.0.events.0.nth_day_is_holiday', 3)
            ->assertJsonPath('data.0.events.0.schedule_settings', $schedule_settings)
            ->assertJsonPath('data.0.events.1.id', 2)
            ->assertJsonPath('data.0.events.1.barbershop_id', 1)
            ->assertJsonPath('data.0.events.1.name', 'Woman Haircut')
            ->assertJsonPath('data.0.events.1.days_duration_slots', 7)
            ->assertJsonPath('data.0.events.1.max_clients_per_slot', 3)
            ->assertJsonPath('data.0.events.1.minutes_every_slots', 60)
            ->assertJsonPath('data.0.events.1.minutes_break_between_slots', 10)
            ->assertJsonPath('data.0.events.1.nth_day_is_holiday', 3)
            ->assertJsonPath('data.0.events.1.schedule_settings', $schedule_settings);
    }

    public function testBarbershopSlots()
    {
        $response = $this->get('/getslots?id=1&dateTime=2022-06-27');
        $response->assertStatus(200)
            ->assertJsonCount(49, 'data')
            ->assertJsonPath('data.08:00', [
                'isAvailable' => true,
                'available_slots' => 3
            ])->assertJsonPath('data.12:00', [
                'isAvailable' => false,
                'available_slots' => 0
            ])->assertJsonPath('data.13:00', [
                'isAvailable' => true,
                'available_slots' => 3
            ]);

        $response = $this->get('/getslots?id=2&dateTime=2022-06-27');
        $response->assertStatus(200)
            ->assertJsonCount(11, 'data')
            ->assertJsonPath('data.08:00', [
                'isAvailable' => true,
                'available_slots' => 3
            ])->assertJsonPath('data.12:40', [
                'isAvailable' => false,
                'available_slots' => 0
            ])->assertJsonPath('data.13:50', [
                'isAvailable' => true,
                'available_slots' => 3
            ]);

        $response = $this->get('/getslots?id=1&dateTime=2022-06-28'); //holiday
        $response->assertStatus(404);

        $response = $this->get('/getslots?id=2&dateTime=2022-06-28'); //holiday
        $response->assertStatus(404);

        $response = $this->get('/getslots?id=1&dateTime=2022-06-20');
        $response->assertStatus(404);

        $response = $this->get('/getslots?id=2&dateTime=2022-06-20');
        $response->assertStatus(404);
    }

    public function testBarbershopBooking()
    {
        $bookDataMen = [
            "id" => 1,
            "dateTime" => "2022-07-01 09:10:00",
            "email" => "alibek@mail.com",
            "firstName" => "Alibek",
            "lastName" => "Yermek"
        ];

        $bookDataWomen = [
            "id" => 2,
            "dateTime" => "2022-07-01 09:10:00",
            "email" => "alibek@mail.com",
            "firstName" => "Alibek",
            "lastName" => "Yermek"
        ];

        $bookWrongData = [
            "id" => 1,
            "dateTime" => "2022-07-01 09:10:00",
            "email" => "alibekNotEmail",
            "firstName" => "Alibek",
        ];

        $response = $this->post('/book', $bookDataMen);
        $response->assertStatus(404);

        $response = $this->post('/book', $bookDataWomen);
        $response->assertStatus(200);

        $response = $this->get('/getslots?id=2&dateTime=2022-07-01');
        $response->assertStatus(200)
            ->assertJsonCount(11, 'data')
            ->assertJsonPath('data.08:00', [
                'isAvailable' => true,
                'available_slots' => 3
            ])->assertJsonPath('data.09:10', [
                'isAvailable' => true,
                'available_slots' => 2
            ]);

        $response = $this->post('/book', $bookDataWomen);
        $response->assertStatus(200);

        $response = $this->get('/getslots?id=2&dateTime=2022-07-01');
        $response->assertStatus(200)
            ->assertJsonCount(11, 'data')
            ->assertJsonPath('data.08:00', [
                'isAvailable' => true,
                'available_slots' => 3
            ])->assertJsonPath('data.09:10', [
                'isAvailable' => true,
                'available_slots' => 1
            ]);

        $response = $this->post('/book', $bookDataWomen);
        $response->assertStatus(200);

        $response = $this->get('/getslots?id=2&dateTime=2022-07-01');
        $response->assertStatus(200)
            ->assertJsonCount(11, 'data')
            ->assertJsonPath('data.08:00', [
                'isAvailable' => true,
                'available_slots' => 3
            ])->assertJsonPath('data.09:10', [
                'isAvailable' => false,
                'available_slots' => 0
            ]);

        $response = $this->post('/book', $bookDataWomen);
        $response->assertStatus(404);

        $response = $this->post('/book', $bookWrongData);
        $response->assertStatus(404);
    }
}

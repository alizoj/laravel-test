<?php

namespace App\Services;

use App\Models\BarbershopAppointment;
use Carbon\Carbon;
use Exception;
use App\Models\BarbershopEvent;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

class BarberShopAppointmentService
{
    /**
     * @param int $event_id
     * @param string $dateTime
     * @return array
     * @throws Exception
     */
    public function getAvailableSlotsByDay(int $event_id, string $dateTime): array
    {
        $event = BarbershopEvent::findOrFail($event_id);
        $settings = $event->schedule_settings;
        $lookingDay = Carbon::createFromFormat('Y-m-d', $dateTime);

        // Sure, this is trash way by getting access directly into array, need DTO
        $event_start_time =
            $settings[BarbershopEvent::SETTING_DAYS][$lookingDay->dayOfWeek][BarbershopEvent::SETTING_START_TIME];
        $event_end_time =
            $settings[BarbershopEvent::SETTING_DAYS][$lookingDay->dayOfWeek][BarbershopEvent::SETTING_END_TIME];
        $event_break_times = Arr::pluck(
            $settings[BarbershopEvent::SETTING_BREAKS],
            BarbershopEvent::SETTING_END_TIME,
            BarbershopEvent::SETTING_START_TIME
        );

        $this->checkCurrentDay($lookingDay, $event, $settings);

        return $this->buildSlots($lookingDay, $event, $event_start_time, $event_end_time, $event_break_times);
    }

    /**
     * @param int $event_id
     * @param string $dateTime
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function cacheGetOrSetAvailableSlotsByDay(int $event_id, string $dateTime): array
    {
        $cacheKey = 'slots_' . $event_id . '_' . $dateTime;
        $cacheData = cache()->get($cacheKey);
        if ($cacheData !== null) {
            return $cacheData;
        }

        $cacheData = $this->getAvailableSlotsByDay(
            $event_id,
            $dateTime
        );
        cache()->set($cacheKey, $cacheData);

        return $cacheData;
    }

    /**
     * @throws Exception|InvalidArgumentException
     */
    public function bookSlot(
        int $event_id,
        string $dateTime,
        string $email,
        string $firstName,
        string $lastName,
    ) {
        $lookingTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateTime);
        DB::beginTransaction();
        try {
            $workTime = $lookingTime->format('H:i');
            $availableTimes = $this->getAvailableSlotsByDay($event_id, $lookingTime->format('Y-m-d'));
            if (Arr::exists($availableTimes, $workTime)) {
                if ($availableTimes[$workTime]['isAvailable'] !== true) {
                    throw new Exception('You can\'t book an appointment', 404);
                }

                $model = BarbershopAppointment::create([
                    'barbershop_event_id' => $event_id,
                    'datetime' => $dateTime,
                    'email' => $email,
                    'firstname' => $firstName,
                    'lastname' => $lastName,
                ]);

                DB::commit();

                return $model;
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), 500);
        }
        DB::rollBack();

        throw new Exception('You can\'t book an appointment', 404);
    }

    /**
     * @throws Exception
     */
    private function checkCurrentDay(Carbon $lookingDay, BarbershopEvent $event, array $settings): void
    {
        $diffDays = $event->created_at->diffInDays($lookingDay->format('Y-m-d H:i:s')) + 1;
        if ($diffDays > $event->days_duration_slots
            || Carbon::now()->diffInDays($lookingDay, false) < 0) {
            throw new Exception('There are no any events', 404);
        }

        if ($settings[$event::SETTING_DAYS][$lookingDay->dayOfWeek][$event::SETTING_WORKDAY] !== true
            || Arr::has($settings[$event::SETTING_HOLIDAYS], $lookingDay->format('Y-m-d'))) {
            throw new Exception('This day is not working day', 404);
        }
    }

    private function buildSlots(
        Carbon $lookingDay,
        BarbershopEvent $event,
        string $start_time,
        string $end_time,
        array $break_times
    ): array {
        $slots = [];
        $startOfWorkDay = $lookingDay->copy()->setTimeFromTimeString($start_time);
        $endOfWorkDay = $lookingDay->copy()->setTimeFromTimeString($end_time);
        $appointments = $this->getAppointmentsByIdAndDay($event->id, $lookingDay, $start_time, $end_time);

        while ($startOfWorkDay < $endOfWorkDay) {
            $slots[$startOfWorkDay->format('H:i')] =
                $this->getAvailableData(
                    $startOfWorkDay,
                    $appointments,
                    $break_times,
                    $event->max_clients_per_slot
                );
            $startOfWorkDay->addMinutes($event->minutes_every_slots + $event->minutes_break_between_slots);
        }

        return $slots;
    }

    /**
     * @param int $eventId
     * @param Carbon $lookingDay
     * @param string $start_time
     * @param string $end_time
     * @return array<BarbershopAppointment>|[]
     */
    private function getAppointmentsByIdAndDay(
        int $eventId,
        Carbon $lookingDay,
        string $start_time,
        string $end_time
    ): array {
        $bookings = BarbershopAppointment::selectRaw(
            'DATE_FORMAT(datetime, "%H:%i") as ' . BarbershopEvent::SETTING_START_TIME . ', count(datetime) as count'
        )
            ->where('barbershop_event_id', $eventId)
            ->whereBetween('datetime', [
                $lookingDay->format('Y-m-d') . ' ' . $start_time . ':00',
                $lookingDay->format('Y-m-d') . ' ' . $end_time . ':59',
            ])
            ->groupBy('datetime')
            ->get()
            ->toArray();

        return Arr::pluck($bookings, 'count', BarbershopEvent::SETTING_START_TIME);
    }

    private function getAvailableData(
        Carbon $startOfWorkDay,
        array $appointments,
        array $break_times,
        int $max_clients_per_slot
    ): array {
        $isAvailable = true;
        foreach ($break_times as $start => $end) {
            $endCompare = $startOfWorkDay->copy()->setTimeFromTimeString($end);
            if (!$startOfWorkDay->equalTo($endCompare)
                && $startOfWorkDay->between(
                    $startOfWorkDay->copy()->setTimeFromTimeString($start),
                    $endCompare,
                    true
                )
            ) {
                $isAvailable = false;
                break;
            }
        }

        $availableSlots = $max_clients_per_slot;
        $workTime = $startOfWorkDay->format('H:i');
        if (Arr::exists($appointments, $workTime)) {
            $availableSlots -= $appointments[$workTime];
        }

        return [
            'isAvailable' => $availableSlots > 0 && $isAvailable,
            'available_slots' => $isAvailable ? max($availableSlots, 0) : 0
        ];
    }
}

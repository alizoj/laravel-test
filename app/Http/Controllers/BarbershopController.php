<?php

namespace App\Http\Controllers;

use App\Models\Barbershop;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\BarberShopAppointmentService;

class BarbershopController extends BaseController
{
    public BarberShopAppointmentService $appointmentService;

    public function __construct(BarberShopAppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function getAvailableSlots(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'bail|required|integer|exists:barbershop_events',
                'dateTime' => 'required|date_format:Y-m-d'
            ]);

            if ($validator->passes()) {
                return $this->sendResponse(
                    $this->appointmentService->cacheGetOrSetAvailableSlotsByDay(
                        $request->id,
                        $request->dateTime
                    )
                );
            }

            return $this->sendError('Validation Error', $validator->errors()->all());
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function bookSlot(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'bail|required|integer|exists:barbershop_events',
                'dateTime' => 'required|date_format:Y-m-d H:i:s',
                'email' => 'required|email',
                'firstName' => 'required|string',
                'lastName' => 'required|string'
            ]);

            if ($validator->passes()) {
                return $this->sendResponse(
                    $this->appointmentService->bookSlot(
                        $request->id,
                        $request->dateTime,
                        $request->email,
                        $request->firstName,
                        $request->lastName,
                    )
                );
            }
            return $this->sendError('Validation Error', $validator->errors()->all());
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function getBarbershops()
    {
        return $this->sendResponse(Barbershop::with('events')->get());
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarberShopAvailableSlotsRequest;
use App\Services\BarberShopAppointmentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
                    $this->appointmentService->getAvailableSlotsByDay(
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
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @param $result
     * @param string $message
     * @return JsonResponse
     */
    public function sendResponse($result, string $message = 'OK'): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @param $error
     * @param array $errorMessages
     * @param int $code
     * @return JsonResponse
     */
    public function sendError($error, array $errorMessages = [], int $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }
}

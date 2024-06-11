<?php

namespace App\Exceptions;

use App\Api\ApiResponse;
use App\Enums\HttpStatus;
use Exception;
use Illuminate\Http\JsonResponse;

class ApiResponseException extends Exception
{
    use ApiResponse;
    public function render(): JsonResponse
    {
        return $this->sendResponse($this->getMessage(), HttpStatus::SERVER_ERROR->value, 'error');
    }
}

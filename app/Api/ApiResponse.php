<?php

namespace App\Api;

use App\Enums\HttpStatus;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public array $response = [];
    public int $httpStatus = 200;

    public function setResponse(mixed $data, int $status, string $wrapKey = 'data'): void
    {
        $this->response[$wrapKey] = $data;
        $this->httpStatus = $status;
    }

    public function sendResponse(mixed $data = null, int $status = 200, $wrapKey = 'data'): JsonResponse
    {
        if ($data) {
            $this->setResponse($data, $status, $wrapKey);
        }

        return response()->json($this->response, $this->httpStatus);
    }
}

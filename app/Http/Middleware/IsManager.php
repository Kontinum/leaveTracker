<?php

namespace App\Http\Middleware;

use App\Api\ApiResponse;
use App\Enums\HttpStatus;
use App\Enums\UserTypes;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsManager
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('sanctum')->user()->user_type_id !== UserTypes::MANAGER->value) {
            return $this->sendResponse('Unauthorized', HttpStatus::UNAUTHORIZED->value, 'message');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Api\ApiResponse;
use App\Enums\HttpStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsRole
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$userTypes): Response
    {
        $authUserType = auth('sanctum')->user()->userType->type;

        if (!in_array($authUserType, $userTypes)) {
            return $this->sendResponse('Unauthorized', HttpStatus::UNAUTHORIZED->value, 'message');
        }

        return $next($request);
    }
}

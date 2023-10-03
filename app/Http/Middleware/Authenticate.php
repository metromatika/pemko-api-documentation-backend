<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use App\Traits;

class Authenticate extends Middleware
{
    use Traits\ApiResponser;

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request
     * @return string|null
     */
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * @param $request
     * @param array $guards
     * @return void
     */
    protected function unauthenticated($request, array $guards): void
    {
        abort($this->errorResponse('Unauthorized', 401));
    }
}

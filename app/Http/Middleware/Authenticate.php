<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  Request  $request
     * @return string|void
     */
    protected function redirectTo($request): string
    {
        session()->flash('error', 'Vous devez être connecté pour accéder à cette page');
        return route('login');
    }
}

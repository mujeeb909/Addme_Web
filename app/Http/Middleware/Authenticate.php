<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (current_func() == 'activateCode') {
            return route('profile', $request->code);
        }

        $isActivationCode = false;
        if (current_func() == 'members_activation_details') {
            $isActivationCode = true;
        }

        if (!$request->expectsJson()) {
            if ($isActivationCode) {
                return route('app_store');
            } else {
                return route('admin.login');
            }
        }
    }
}

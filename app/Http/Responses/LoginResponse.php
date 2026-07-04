<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        if ($request->user()?->is_super_admin) {
            return $request->wantsJson()
                ? new JsonResponse(['two_factor' => false], 200)
                : redirect()->route('admin.dashboard');
        }

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 200)
            : redirect()->intended(config('fortify.home'));
    }
}

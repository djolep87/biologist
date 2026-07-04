<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Contracts\EmailVerificationNotificationSentResponse;
use Laravel\Fortify\Http\Responses\RedirectAsIntended;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse|mixed
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? new JsonResponse('', 204)
                : app(RedirectAsIntended::class, ['name' => 'email-verification']);
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            Log::error('Email verification send failed', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'email' => 'Email nije poslat. Proverite MAIL_* podešavanja u .env fajlu (Mailtrap SMTP).',
            ]);
        }

        return app(EmailVerificationNotificationSentResponse::class);
    }
}

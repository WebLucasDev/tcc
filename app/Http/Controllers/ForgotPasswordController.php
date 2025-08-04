<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{

    public function sendResetLink(ForgotPasswordRequest $request)
    {

        $this->ensureIsNotRateLimited($request);

        $email = $request->getEmail();

        // Aqui você pode implementar o envio de email customizado
        // Por enquanto, vamos simular o envio bem-sucedido

        // Limpa o rate limiting em caso de sucesso
        RateLimiter::clear($this->throttleKey($request));

        return response()->json([
            'success' => true,
            'message' => 'Instruções de redefinição de senha enviadas por email.'
        ]);
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 3)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => "Muitas tentativas de redefinição. Tente novamente em {$seconds} segundos.",
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return 'password-reset:' . strtolower((string) $request->input('email')) . '|' . $request->ip();
    }
}

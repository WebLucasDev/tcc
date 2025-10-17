<?php

namespace App\Http\Controllers;

use App\Http\Requests\login\ProcessResetRequest;
use App\Http\Requests\login\SendRequest;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function send(SendRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'E-mail inválido!',
                ], 404);
            }

            $token = Str::random(64);

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]);

            $resetUrl = route('forgot-password.open-reset', ['token' => $token]);

            Mail::to($request->email)->send(new ForgotPasswordMail($user, $token, $resetUrl));

            return response()->json([
                'success' => true,
                'message' => 'Instruções de alteração de senha enviadas por email!',
            ], 200);

        } catch (\Exception $e) {

            Log::error('Erro ao enviar email de recuperação de senha: '.$e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.',
            ], 500);
        }
    }

    public function openReset(Request $request, $token)
    {

        $passwordReset = DB::table('password_reset_tokens')
            ->where('created_at', '>', Carbon::now()->subHours(24))
            ->first();

        if (! $passwordReset) {
            return redirect()->route('login.index')->with('error', 'Token inválido ou expirado.');
        }

        return view('public.login.forgot-password', compact('token'));
    }

    public function processReset(ProcessResetRequest $request)
    {
        try {

            $passwordReset = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('created_at', '>', Carbon::now()->subHours(24))
                ->first();

            if (! $passwordReset || ! Hash::check($request->token, $passwordReset->token)) {
                return back()->with('error', 'Token inválido ou expirado.');
            }

            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return redirect()->route('login.index')->with('success', 'Senha alterada com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro interno do servidor. Tente novamente.');
        }
    }

    public function checkCurrentPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
        ]);

        $user = Auth::user();

        if (Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => true,
                'message' => 'Senha atual está correta.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Senha atual incorreta.',
        ], 422);
    }
}

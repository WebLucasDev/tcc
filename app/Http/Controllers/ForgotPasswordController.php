<?php

namespace App\Http\Controllers;

use App\Http\Requests\login\ProcessResetRequest;
use App\Http\Requests\login\SendRequest;
use App\Mail\ForgotPasswordMail;
use App\Models\CollaboratorModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Envia email de recuperação de senha
     */
    public function send(SendRequest $request)
    {
        try {
            // Busca em ambas as tabelas (users e collaborators)
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $user = CollaboratorModel::where('email', $request->email)->first();
            }

            if (!$user) {
                return back()->with('error', 'E-mail não encontrado.');
            }

            // Gera token único
            $token = Str::random(64);

            // Remove tokens antigos e cria novo
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            // Envia email
            $resetUrl = route('forgot-password.open-reset', [
                'token' => $token,
                'email' => $request->email
            ]);

            Mail::to($request->email)->send(new ForgotPasswordMail($user, $resetUrl));

            return back()->with('success', 'Instruções de alteração de senha enviadas por email!');

        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de recuperação de senha', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erro ao enviar email. Tente novamente.');
        }
    }

    /**
     * Abre página de redefinição de senha
     */
    public function openReset(Request $request, string $token)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('login.index')
                ->with('error', 'Link inválido.');
        }

        // Verifica se existe token válido
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('created_at', '>', now()->subHours(24))
            ->first();

        if (!$passwordReset) {
            return redirect()->route('login.index')
                ->with('error', 'Token inválido ou expirado.');
        }

        return view('public.login.forgot-password', compact('token', 'email'));
    }

    /**
     * Processa redefinição de senha
     */
    public function processReset(ProcessResetRequest $request)
    {
        try {
            // Busca token válido
            $passwordReset = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('created_at', '>', now()->subHours(24))
                ->first();

            if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
                return back()
                    ->withInput()
                    ->with('error', 'Token inválido ou expirado.');
            }

            // Busca usuário em ambas as tabelas
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $user = CollaboratorModel::where('email', $request->email)->first();
            }

            if (!$user) {
                return back()->with('error', 'Usuário não encontrado.');
            }

            // Atualiza senha
            // O CollaboratorModel tem mutator, User tem cast 'hashed'
            // Ambos funcionam com atribuição direta
            $user->password = $request->password;
            $user->save();

            // Remove token usado
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return redirect()->route('login.index')
                ->with('success', 'Senha alterada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao processar reset de senha', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erro ao processar solicitação. Tente novamente.');
        }
    }

    /**
     * Verifica se a senha atual está correta
     */
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

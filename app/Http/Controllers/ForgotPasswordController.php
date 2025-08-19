<?php

namespace App\Http\Controllers;

use App\Http\Requests\web\login\SendRequest;
<<<<<<< HEAD
use App\Http\Requests\web\login\ProcessResetRequest;
=======
>>>>>>> d92081d90cde19d841f0c979b170eb0fc725a80b
use App\Models\User;
use App\Mail\ForgotPasswordMail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
<<<<<<< HEAD
use Illuminate\Support\Facades\Log;
=======
>>>>>>> d92081d90cde19d841f0c979b170eb0fc725a80b
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
<<<<<<< HEAD
    public function send(SendRequest $request)
    {
        try {
=======
    public function send(SendRequest $request){

        try{

>>>>>>> d92081d90cde19d841f0c979b170eb0fc725a80b
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'E-mail inválido!'
                ], 404);
            }

            $token = Str::random(64);

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]);

<<<<<<< HEAD
            // Envia o email
            $resetUrl = route('forgot-password.open-reset', ['token' => $token]);

            Mail::to($request->email)->send(new ForgotPasswordMail($user, $token, $resetUrl));

            return response()->json([
                'success' => true,
                'message' => 'Instruções de alteração de senha enviadas por email!'
            ], 200);

        } catch (\Exception $e) {
            // Log do erro para debug
            Log::error('Erro ao enviar email de recuperação de senha: ' . $e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.',
                'debug' => config('app.debug') ? $e->getMessage() : null
=======
            Mail::send('login.email.forgot-password', [
                'token' => $token,
                'email' => $request->email,
                'user' => $user
            ], function($message) use ($request) {
                $message->to($request->email);
                $message->subject('Password Reset');
            });

            return response()->json([
                'success' => true,
                'message' => 'Recovery email sent successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending recovery email'
>>>>>>> d92081d90cde19d841f0c979b170eb0fc725a80b
            ], 500);
        }
    }

<<<<<<< HEAD
    public function openReset(Request $request, $token)
    {
        // Verifica se o token existe e ainda é válido (24 horas)
        $passwordReset = DB::table('password_reset_tokens')
            ->where('created_at', '>', Carbon::now()->subHours(24))
            ->first();

        if (!$passwordReset) {
            return redirect()->route('login.index')->with('error', 'Token inválido ou expirado.');
        }

        return view('login.forgot-password', compact('token'));
    }

    public function processReset(ProcessResetRequest $request)
    {
        try {
            // Verifica se o token existe e ainda é válido
            $passwordReset = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('created_at', '>', Carbon::now()->subHours(24))
                ->first();

            if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
                return back()->with('error', 'Token inválido ou expirado.');
            }

            // Atualiza a senha do usuário
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            // Remove o token usado
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return redirect()->route('login.index')->with('success', 'Senha alterada com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro interno do servidor. Tente novamente.');
        }
=======
    public function openReset($token, Request $request){

        return view('login.forgot-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function processReset(Request $request){
        
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'token' => 'required'
        ]);

        $resetData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if(!$resetData || !Hash::check($request->token, $resetData->token)){
            return back()->withErrors(['error' => 'Invalid token!']);
        }

        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login.index')->with('status', 'Password updated successfully!');
>>>>>>> d92081d90cde19d841f0c979b170eb0fc725a80b
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
                'message' => 'Senha atual está correta.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Senha atual incorreta.'
        ], 422);
    }
}

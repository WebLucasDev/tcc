<?php

namespace App\Http\Controllers;

use App\Http\Requests\web\login\SendRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function send(SendRequest $request){

        try{

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
            ], 500);
        }
    }

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
    }

    public function checkCurrentPassword(){

    }
}

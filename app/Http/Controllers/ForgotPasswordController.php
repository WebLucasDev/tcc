<?php

namespace App\Http\Controllers;

use App\Http\Requests\web\login\SendingResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function send(SendingResetPasswordRequest $request){

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
        }
        catch () {

        };
    }

    public function openReset(){

        return view('login')
    }

    public function processReset(){

    }

    public function checkCurrentPassword(){

    }
}

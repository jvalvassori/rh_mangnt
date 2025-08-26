<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ConfirmAccountController extends Controller
{
    public function confirmAccount($token)
    {
       // check if the token is valid 
       $user = User::where('confirmation_token', $token)->first();

       if(!$user){
            abort(403, 'Invalid confimation token');
       }

       return view('auth.confirm-account', compact('user'));
    }

    public function confirmAccountSubmit(Request $request)
    {
        // form validation 
        $request->validate([
            'token' => 'required|string|size:60',
            'password' => 'required|confirmed|min:8|max:16|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
        ],
        [
                'token.required' => 'O token é obrigatório',
                'password.required' => 'A senha é obrigátoria',
                'password_confirmation.required' => 'A confirmação de senha é obrigátoria',
                'password.confirmed' => 'A confirmação de senha deve ser a mesma, tente novamente',
                'password.regex' => 'Precisa de um caracter especial, letra maiuscula, minuscula e um numero'
        ]);

        $user = User::where('confirmation_token', $request->token)->first();
        $user->password = bcrypt($request->password);
        $user->confirmation_token = null;
        $user->email_verified_at = now();
        $user->save();

        return view('auth.welcome', compact('user'));
    }
}

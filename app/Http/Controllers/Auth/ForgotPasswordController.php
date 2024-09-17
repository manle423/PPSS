<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Notifications\CustomResetPassword;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    protected function sendResetLinkResponse(Request $request, $response)
    {
        $user = $this->broker()->getUser($request->only('email'));

        if ($user) {
            $user->notify(new CustomResetPassword($this->broker()->createToken($user)));
        }

        return back()->with('status', trans($response));
    }
}

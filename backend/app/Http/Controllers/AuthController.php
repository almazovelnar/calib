<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\ReCaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Cookie;

class AuthController extends Controller {

    /**
     * Login user
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request) : RedirectResponse {
        $request->validate([
            'email'           => 'required|email',
            'password'        => 'required|string',
            'recaptcha-token' => ['required', new ReCaptcha($request->get('_token'))]
        ]);

        $user = User::whereEmail($request->get('email'))->get();

        if(!$user = $user->first()) {
            // todo: increase failed login attempt count
            return back()->withInput()->with('error', __('auth.failed'));
        }

        if(!Hash::check($request->get("password"), $user->password)) {
            // todo: increase failed login attempt count
            return back()->withInput()->with('error', __('auth.failed'));
        }

        // todo: reset failed login attempt count
        auth()->login($user);

        $num_of_minutes_until_expire = 60 * 24 * 7; // one week
        \Cookie::queue('edit', 'true', $num_of_minutes_until_expire, null, '.caliber.az');

        return redirect()->route('index');
    }

    /**
     * Logout user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout() : RedirectResponse {
        auth()->logout();

        session()->invalidate();

        $cookie = \Cookie::forget('edit','/','.caliber.az');

        return redirect()->route('login')->withCookie($cookie);
    }

}

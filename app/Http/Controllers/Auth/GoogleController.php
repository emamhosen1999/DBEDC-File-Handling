<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('google_id', $googleUser->id)->first();

        if ($user) {
            $user->update([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'avatar_url' => $googleUser->avatar,
                'last_login' => now(),
            ]);
        } else {
            $user = User::create([
                'id' => $this->generateUlid(),
                'google_id' => $googleUser->id,
                'email' => $googleUser->email,
                'name' => $googleUser->name,
                'avatar_url' => $googleUser->avatar,
                'provider' => 'google',
                'role' => $this->determineRole($googleUser->email),
                'last_login' => now(),
            ]);
        }

        Auth::login($user, true);

        return redirect()->intended('/dashboard');
    }

    private function determineRole($email)
    {
        $adminEmail = env('ADMIN_EMAIL');
        
        if ($adminEmail && $email === $adminEmail) {
            return 'ADMIN';
        }

        if (User::count() === 0) {
            return 'ADMIN';
        }

        return 'MEMBER';
    }

    private function generateUlid(): string
    {
        $time = str_pad(base_convert((int)(microtime(true) * 1000), 10, 32), 10, '0', STR_PAD_LEFT);
        $random = '';
        $chars = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
        for ($i = 0; $i < 16; $i++) {
            $random .= $chars[random_int(0, 31)];
        }
        return strtoupper($time . $random);
    }
}

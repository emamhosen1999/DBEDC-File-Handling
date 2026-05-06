<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WeChatController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('wechat')->redirect();
    }

    public function callback()
    {
        $wechatUser = Socialite::driver('wechat')->user();

        $user = User::where('wechat_openid', $wechatUser->id)->first();

        if ($user) {
            $user->update([
                'name' => $wechatUser->nickname ?? $wechatUser->name,
                'avatar_url' => $wechatUser->avatar,
                'wechat_unionid' => $wechatUser->unionid ?? null,
                'last_login' => now(),
            ]);
        } else {
            $user = User::create([
                'id' => $this->generateUlid(),
                'google_id' => 'wechat_' . $wechatUser->id,
                'email' => $wechatUser->email ?? 'wechat_' . $wechatUser->id . '@placeholder.com',
                'name' => $wechatUser->nickname ?? $wechatUser->name,
                'avatar_url' => $wechatUser->avatar,
                'wechat_openid' => $wechatUser->id,
                'wechat_unionid' => $wechatUser->unionid ?? null,
                'provider' => 'wechat',
                'role' => $this->determineRole($wechatUser->email ?? null),
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

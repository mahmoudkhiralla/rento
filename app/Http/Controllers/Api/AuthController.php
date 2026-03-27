<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\PointsTransaction;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    protected function getFingerprint(Request $request)
    {
        $fp = $request->cookie('fp');
        if ($fp) {
            return $fp;
        }
        $headerFp = $request->header('X-Fingerprint');
        if ($headerFp) {
            return $headerFp;
        }
        $base = ($request->header('User-Agent') ?? '').'|'.($request->header('Accept-Language') ?? '').'|'.($request->ip() ?? '');

        return substr(hash('sha256', $base), 0, 32);
    }

    public function referralLink(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $link = url('/register').'?ref='.$user->id;
        $fp = $this->getFingerprint($request);

        return response()->json(['link' => $link])->withCookie(cookie('fp', $fp, 525600, '/', null, false, true, false, 'Lax'));
    }

    public function register(AuthRegisterRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        // Referral points awarding if ref parameter present
        try {
            $referrerId = (int) ($request->input('ref') ?? 0);
            if ($referrerId > 0 && Setting::get('points_enabled', true) && User::where('id', $referrerId)->exists()) {
                $wallet = Wallet::firstOrCreate(['user_id' => $referrerId], ['balance' => 0, 'points_balance' => 0]);
                $fp = $this->getFingerprint($request);
                $exists = PointsTransaction::where('wallet_id', $wallet->id)
                    ->where('type', 'earn')
                    ->where('reason', 'like', 'Referral:'.$user->id.'%')
                    ->exists();
                if (! $exists) {
                    $points = (int) Setting::get('points_per_transaction', 100);
                    $wallet->points_balance = ($wallet->points_balance ?? 0) + $points;
                    $wallet->save();
                    PointsTransaction::create([
                        'wallet_id' => $wallet->id,
                        'points' => $points,
                        'type' => 'earn',
                        'reason' => 'Referral:'.$user->id.':FP:'.$fp,
                    ]);
                }
            }
        } catch (\Throwable $e) {
        }

        $token = $user->createToken('api')->plainTextToken;
        $fp = $this->getFingerprint($request);

        return response()->json(['token' => $token, 'user' => $user])->withCookie(cookie('fp', $fp, 525600, '/', null, false, true, false, 'Lax'));
    }

    public function login(AuthLoginRequest $request)
    {
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! password_verify($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function forgotPassword(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        $user = User::where('email', $data['email'])->first();
        $code = (string) random_int(100000, 999999);
        $token = hash('sha256', $code);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $data['email']],
            ['token' => $token, 'created_at' => now()]
        );
        if ($user) {
            try {
                Mail::raw('رمز التأكيد لإعادة تعيين كلمة المرور: '.$code, function ($message) use ($data) {
                    $message->to($data['email'])->subject('رمز تأكيد إعادة تعيين كلمة المرور');
                });
            } catch (\Throwable $e) {
                \Log::info('Password reset code', ['email' => $data['email'], 'code' => $code]);
            }
        }
        return response()->json(['message' => 'تم إرسال رمز التأكيد إن وجد البريد'], 200);
    }

    public function verifyResetCode(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);
        $row = DB::table('password_reset_tokens')->where('email', $data['email'])->first();
        if (! $row) {
            return response()->json(['message' => 'الرمز غير صالح'], 422);
        }
        $expired = $row->created_at && Carbon::parse($row->created_at)->lt(now()->subMinutes(30));
        if ($expired) {
            return response()->json(['message' => 'انتهت صلاحية الرمز'], 422);
        }
        $ok = hash('sha256', (string) $data['code']) === $row->token;
        if (! $ok) {
            return response()->json(['message' => 'الرمز غير صحيح'], 422);
        }
        return response()->json(['message' => 'تم التحقق من الرمز بنجاح'], 200);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);
        $row = DB::table('password_reset_tokens')->where('email', $data['email'])->first();
        if (! $row) {
            return response()->json(['message' => 'الرمز غير صالح'], 422);
        }
        $expired = $row->created_at && Carbon::parse($row->created_at)->lt(now()->subMinutes(30));
        if ($expired) {
            return response()->json(['message' => 'انتهت صلاحية الرمز'], 422);
        }
        $ok = hash('sha256', (string) $data['code']) === $row->token;
        if (! $ok) {
            return response()->json(['message' => 'الرمز غير صحيح'], 422);
        }
        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            return response()->json(['message' => 'المستخدم غير موجود'], 404);
        }
        $user->password = Hash::make($data['password']);
        $user->save();
        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();
        return response()->json(['message' => 'تم تعيين كلمة المرور الجديدة بنجاح'], 200);
    }
}

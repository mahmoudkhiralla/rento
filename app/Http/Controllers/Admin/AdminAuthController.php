<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard.index'));
        }

        return back()->withErrors([
            'email' => 'بيانات الدخول غير صحيحة.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email,'.$admin->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->phone = $validated['phone'] ?? $admin->phone;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('admins', 'public');
            $admin->image = 'storage/'.$path;
        }

        $admin->save();

        return redirect()->route('admin.profile')->with('status', 'تم تحديث الملف الشخصي بنجاح');
    }

    public function updatePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ], [
            'current_password.required' => 'يرجى إدخال كلمة المرور الحالية.',
            'password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'password.min' => 'يجب أن تكون كلمة المرور 8 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'password_confirmation.required' => 'يرجى تأكيد كلمة المرور الجديدة.',
        ]);

        if (! Hash::check($request->input('current_password'), $admin->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }

        $admin->password = Hash::make($request->input('password'));
        $admin->save();

        return redirect()->route('admin.profile')->with('status', 'تم تحديث كلمة المرور بنجاح');
    }
}

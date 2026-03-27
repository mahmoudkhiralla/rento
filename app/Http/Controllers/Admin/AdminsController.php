<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminsController extends Controller
{
    public function index()
    {
        $admins = Admin::orderByDesc('id')->paginate(10);

        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => 'يرجى إدخال الاسم الكامل.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',
            'password.required' => 'يرجى إدخال كلمة المرور.',
            'password.min' => 'يجب أن تكون كلمة المرور 8 أحرف على الأقل.',
            'phone.max' => 'رقم الهاتف طويل جداً.',
            'image.image' => 'الصورة غير صالحة.',
            'image.max' => 'حجم الصورة كبير جداً (الحد 2MB).',
        ]);

        $admin = new Admin;
        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->password = Hash::make($validated['password']);
        $admin->phone = $validated['phone'] ?? null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('admins', 'public');
            $admin->image = $path; // will be referenced via asset('storage/' . $admin->image)
        }

        $admin->save();

        return redirect()->route('admin.admins.index')->with('status', 'تم إضافة مشرف جديد بنجاح');
    }

    public function show(Admin $admin)
    {
        return view('admin.admins.show', compact('admin'));
    }

    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email,'.$admin->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => 'يرجى إدخال الاسم الكامل.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',
            'password.min' => 'يجب أن تكون كلمة المرور 8 أحرف على الأقل.',
            'phone.max' => 'رقم الهاتف طويل جداً.',
            'image.image' => 'الصورة غير صالحة.',
            'image.max' => 'حجم الصورة كبير جداً (الحد 2MB).',
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->phone = $validated['phone'] ?? $admin->phone;

        if (! empty($validated['password'] ?? null)) {
            $admin->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('admins', 'public');
            $admin->image = $path; // will be referenced via asset('storage/' . $admin->image)
        }

        $admin->save();

        return redirect()->route('admin.admins.index')->with('status', 'تم تحديث بيانات المشرف بنجاح');
    }

    public function destroy(Admin $admin)
    {
        $admin->delete();

        return redirect()->route('admin.admins.index')->with('status', 'تم حذف المشرف بنجاح');
    }
}

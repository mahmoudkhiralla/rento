<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminNotificationMail;
use App\Services\SmsService;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Notification::query()->latest();

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // فلترة حسب القناة
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        // فلترة حسب المستهدفين
        if ($request->filled('target_users')) {
            $query->where('target_users', $request->target_users);
        }

        if (! $request->filled('search')) {
            $select = [
                \DB::raw('MIN(id) as id'),
                'title',
                'message',
                'type',
                'channel',
                'target_users',
                'sent_at',
                \DB::raw('COUNT(*) as recipients'),
            ];

            $query = Notification::select($select)
                ->when($request->filled('channel'), function ($q) use ($request) {
                    $q->where('channel', $request->channel);
                })
                ->when($request->filled('target_users'), function ($q) use ($request) {
                    $q->where('target_users', $request->target_users);
                })
                ->groupBy('title', 'message', 'type', 'channel', 'target_users', 'sent_at')
                ->latest('sent_at');
        } else {
            $query = Notification::with('user')->latest();
        }

        $notifications = $query->paginate(10)->withQueryString();

        return view('dashboard.notifications.notifications', compact('notifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.notifications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:alert,booking_confirm,booking_completed,booking_new_request,booking_cancelled',
            'channel' => 'required|in:sms,email,app',
            'target_users' => 'required|in:all,tenants,landlords',
        ]);

        $validated['sent_at'] = now();

        // إرسال وإنشاء إشعار لكل مستخدم مستهدف حسب القناة
        $sendToUser = function (User $user) use ($validated) {
            // داخل التطبيق: إنشاء سجل فقط
            Notification::create([
                'user_id' => $user->id,
                'title' => $validated['title'],
                'message' => $validated['message'],
                'type' => $validated['type'],
                'channel' => $validated['channel'],
                'target_users' => $validated['target_users'],
                'sent_at' => $validated['sent_at'],
            ]);

            if ($validated['channel'] === 'email' && ($user->email ?? null)) {
                try {
                    Mail::to($user->email)->send(new AdminNotificationMail($validated['title'], $validated['message']));
                } catch (\Throwable $e) {
                    \Log::error('Failed to send email notification', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                }
            } elseif ($validated['channel'] === 'sms' && ($user->phone ?? null)) {
                try {
                    SmsService::send((string) $user->phone, (string) $validated['message']);
                } catch (\Throwable $e) {
                    \Log::error('Failed to send SMS notification', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                }
            }
        };

        // إذا كان الهدف "الكل" أو فئة معينة، نحتاج إلى إنشاء إشعار لكل مستخدم
        if ($validated['target_users'] === 'all') {
            $users = User::all();
            foreach ($users as $user) { $sendToUser($user); }
        } elseif ($validated['target_users'] === 'tenants') {
            $users = User::where('user_type', 'tenant')->orWhere('user_type', 'both')->get();
            foreach ($users as $user) { $sendToUser($user); }
        } elseif ($validated['target_users'] === 'landlords') {
            $users = User::where('user_type', 'landlord')->orWhere('user_type', 'both')->get();
            foreach ($users as $user) { $sendToUser($user); }
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'تم إرسال الإشعار بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $notification = Notification::with('user')->findOrFail($id);

        return view('dashboard.notifications.show', compact('notification'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $notification = Notification::findOrFail($id);

        return view('dashboard.notifications.edit', compact('notification'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $notification = Notification::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $notification->update($validated);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'تم تحديث الإشعار بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'تم حذف الإشعار بنجاح');
    }
}

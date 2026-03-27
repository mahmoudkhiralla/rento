<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandlordTask;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandlordTaskController extends Controller
{
    /**
     * قائمة مهام المؤجر الحالي (مصادقة مطلوبة).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // التحقق من صلاحيات المؤجر
        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin'));
        $isLandlordRole = (method_exists($user, 'hasRole') && $user->hasRole('landlord'));
        $isLandlordType = in_array(($user->user_type ?? null), ['landlord', 'both'], true);
        if (! ($isAdmin || $isLandlordRole || $isLandlordType)) {
            return response()->json(['message' => 'يتطلب صلاحيات مؤجر'], 403);
        }

        $query = LandlordTask::with(['property:id,title'])
            ->where('user_id', $user->id)
            ->latest('id');

        if ($request->filled('property_id')) {
            $query->where('property_id', (int) $request->input('property_id'));
        }

        $perPage = (int) $request->integer('per_page', 10);
        $perPage = max(1, min($perPage, 50));

        return response()->json([
            'success' => true,
            'data' => $query->paginate($perPage)->withQueryString(),
        ]);
    }

    /**
     * إنشاء مهمة/تذكير جديد للمؤجر الحالي.
     * الحقول المطلوبة:
     * - title: عنوان التذكير
     * - property_id: معرّف العقار (يجب أن يكون من عقارات المؤجر)
     * - type: نوع التذكير (tenant_request | unit_maintenance | tenant_complaint | unit_cleaning)
     * - scheduled_at أو date & time
     * - message: موضوع النص
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // السماح فقط للمؤجر
        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin'));
        $isLandlordRole = (method_exists($user, 'hasRole') && $user->hasRole('landlord'));
        $isLandlordType = in_array(($user->user_type ?? null), ['landlord', 'both'], true);
        if (! ($isAdmin || $isLandlordRole || $isLandlordType)) {
            return response()->json(['message' => 'يتطلب صلاحيات مؤجر'], 403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'property_id' => 'required|integer|exists:properties,id',
            'type' => 'required|string',
            'scheduled_at' => 'nullable|date',
            'date' => 'nullable|date',
            'time' => 'nullable|date_format:H:i',
            'message' => 'nullable|string',
        ]);

        // تأكد أن العقار يخص هذا المؤجر
        $property = Property::select('id', 'user_id', 'title')->findOrFail($data['property_id']);
        if ($property->user_id !== $user->id) {
            return response()->json(['message' => 'العقار المحدد لا يخص هذا المؤجر'], 403);
        }

        // تطبيع قيمة النوع (قبول العربي وتحويله إلى قيم ثابتة بالإنجليزية)
        $typeMap = [
            'طلب خاص للمستأجر' => 'tenant_request',
            'صيانة الوحدة' => 'unit_maintenance',
            'التعامل مع شكوى للمستأجر' => 'tenant_complaint',
            'ارسال فريق تنظيف الوحدة' => 'unit_cleaning',
        ];
        $rawType = (string) ($data['type'] ?? '');
        $normalizedType = $typeMap[$rawType] ?? strtolower($rawType);
        $allowed = ['tenant_request', 'unit_maintenance', 'tenant_complaint', 'unit_cleaning'];
        if (! in_array($normalizedType, $allowed, true)) {
            return response()->json([
                'message' => 'نوع التذكير غير صحيح',
                'allowed' => $allowed,
            ], 422);
        }

        // احتساب التاريخ/الوقت
        $scheduledAt = null;
        if (! empty($data['scheduled_at'])) {
            $scheduledAt = Carbon::parse($data['scheduled_at']);
        } elseif (! empty($data['date'])) {
            $scheduledAt = Carbon::parse($data['date'].(! empty($data['time']) ? (' '.$data['time']) : ' 00:00'));
        }

        $task = LandlordTask::create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'title' => $data['title'],
            'type' => $normalizedType,
            'scheduled_at' => $scheduledAt,
            'message' => $data['message'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'task' => $task->load('property:id,title'),
        ], 201);
    }

    /**
     * حذف مهمة المؤجر حسب المعرّف.
     */
    public function destroy(Request $request, int $id)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin'));
        $isLandlordRole = (method_exists($user, 'hasRole') && $user->hasRole('landlord'));
        $isLandlordType = in_array(($user->user_type ?? null), ['landlord', 'both'], true);
        if (! ($isAdmin || $isLandlordRole || $isLandlordType)) {
            return response()->json(['message' => 'يتطلب صلاحيات مؤجر'], 403);
        }

        $task = LandlordTask::find($id);
        if (! $task) {
            return response()->json(['message' => 'لم يتم العثور على المهمة'], 404);
        }
        if (! $isAdmin && $task->user_id !== $user->id) {
            return response()->json(['message' => 'غير مصرح بحذف هذه المهمة'], 403);
        }

        $task->delete();

        return response()->json(['success' => true]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupportTicketResource;
use App\Models\SupportTicket;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $priority = $request->get('priority');
        $category = $request->get('category');
        $search = $request->get('q');

        $query = SupportTicket::with(['user', 'tenant', 'landlord', 'property', 'booking', 'assignedTo']);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($priority && $priority !== 'all') {
            $query->where('priority', $priority);
        }

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = (int) $request->integer('per_page', 10);
        $tickets = $query->latest()->paginate($perPage)->withQueryString();

        return SupportTicketResource::collection($tickets);
    }

    public function show($id)
    {
        $ticket = SupportTicket::with(['user', 'assignedTo', 'tenant', 'landlord', 'property', 'booking', 'replies.user'])->findOrFail($id);

        return new SupportTicketResource($ticket);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'category' => 'nullable|string|max:255',
            'booking_id' => 'nullable|exists:bookings,id',
            'property_id' => 'nullable|exists:properties,id',
            'tenant_id' => 'nullable|exists:users,id',
            'landlord_id' => 'nullable|exists:users,id',
        ]);

        $submittedBy = in_array(($user->user_type ?? null), ['landlord', 'both'], true) ? 'landlord' : 'tenant';

        $booking = null;
        $property = null;
        if (! empty($data['booking_id'])) {
            $booking = Booking::with(['property.user', 'user'])->find($data['booking_id']);
            $property = $booking?->property;
            $data['property_id'] = $data['property_id'] ?? optional($property)->id;
        } elseif (! empty($data['property_id'])) {
            $property = Property::with('user')->find($data['property_id']);
        }

        $landlordId = $data['landlord_id'] ?? ($submittedBy === 'landlord' ? $user->id : optional($property?->user)->id);
        $tenantId = $data['tenant_id'] ?? ($submittedBy === 'tenant' ? $user->id : optional($booking?->user)->id);

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'submitted_by' => $submittedBy,
            'tenant_id' => $tenantId,
            'landlord_id' => $landlordId,
            'property_id' => $data['property_id'] ?? null,
            'booking_id' => $data['booking_id'] ?? null,
            'subject' => $data['subject'],
            'description' => $data['description'],
            'status' => 'open',
            'priority' => $data['priority'] ?? 'medium',
            'category' => $data['category'] ?? null,
        ]);

        $ticket->load(['user', 'tenant', 'landlord', 'property', 'booking']);

        return new SupportTicketResource($ticket);
    }

    public function reply(Request $request, $id)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::findOrFail($id);

        \App\Models\SupportTicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'is_admin_reply' => false,
        ]);

        $ticket->update(['last_replied_at' => now()]);

        $ticket->load(['user', 'tenant', 'landlord', 'property', 'booking', 'replies.user']);

        return new SupportTicketResource($ticket);
    }

    public function replySystem(Request $request, $id)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $isAdmin = method_exists($user, 'hasRole') && $user->hasRole('admin');
        if (! $isAdmin) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::findOrFail($id);

        $adminId = $ticket->assigned_to ?: optional(\App\Models\Admin::first())->id;

        \App\Models\SupportTicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $ticket->user_id,
            'admin_id' => $adminId,
            'message' => $request->message,
            'is_admin_reply' => true,
        ]);

        $ticket->update(['last_replied_at' => now()]);

        $ticket->load(['user', 'tenant', 'landlord', 'property', 'booking', 'replies.user']);

        return new SupportTicketResource($ticket);
    }
}
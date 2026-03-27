<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SupportTicketsController extends Controller
{
    /**
     * Display a listing of support tickets.
     */
    public function index(Request $request)
    {
        $statusFilter = $request->get('status');
        // Get statistics
        $totalComplaints = SupportTicket::count();
        $openComplaints = SupportTicket::open()->count();
        $closedComplaints = SupportTicket::closed()->count();

        // Get all tickets ordered by latest
        $listQuery = SupportTicket::with(['user', 'assignedTo'])->latest();
        if ($statusFilter === 'open') {
            $listQuery->open();
        } elseif ($statusFilter === 'closed') {
            $listQuery->closed();
        }
        $complaintsList = $listQuery->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'name' => $ticket->user->name ?? 'مستخدم غير معروف',
                    'desc' => \Str::limit($ticket->subject, 40),
                    'time' => $ticket->created_at->locale('ar')->diffForHumans(),
                    'status' => $this->mapIndicator($ticket),
                    'avatar' => $this->getInitials($ticket->user->name ?? 'UN'),
                    'unread' => empty($ticket->admin_read_at),
                    'avatar_url' => $ticket->user->avatar ?? null,
                ];
            });

        $complaint = null;
        $complaintContext = null;

        return view('dashboard.support.tickets', compact(
            'totalComplaints',
            'openComplaints',
            'closedComplaints',
            'complaintsList',
            'complaint',
            'complaintContext'
        ));
    }

    /**
     * Show the details of a specific ticket.
     */
    public function show($id)
    {
        $statusFilter = request()->get('status');
        // Get statistics
        $totalComplaints = SupportTicket::count();
        $openComplaints = SupportTicket::open()->count();
        $closedComplaints = SupportTicket::closed()->count();

        // Get all tickets for the list
        $listQuery = SupportTicket::with(['user', 'assignedTo'])->latest();
        if ($statusFilter === 'open') {
            $listQuery->open();
        } elseif ($statusFilter === 'closed') {
            $listQuery->closed();
        }
        $complaintsList = $listQuery->get()
            ->map(function ($ticket) use ($id) {
                $isActive = $ticket->id == $id;

                return [
                    'id' => $ticket->id,
                    'name' => $ticket->user->name ?? 'مستخدم غير معروف',
                    'desc' => \Str::limit($ticket->subject, 40),
                    'time' => $ticket->created_at->locale('ar')->diffForHumans(),
                    'status' => $this->mapIndicator($ticket),
                    'avatar' => $this->getInitials($ticket->user->name ?? 'UN'),
                    'active' => $isActive,
                    'unread' => empty($ticket->admin_read_at),
                    'avatar_url' => $ticket->user->avatar ?? null,
                ];
            });

        // Get the selected ticket
        $complaint = SupportTicket::with(['user', 'assignedTo', 'tenant', 'landlord', 'property', 'booking', 'replies.user'])->findOrFail($id);
        if (Schema::hasColumn('support_tickets', 'admin_read_at') && empty($complaint->admin_read_at)) {
            $complaint->admin_read_at = now();
            $complaint->save();
        }
        $complaintContext = $this->buildComplaintContext($complaint);

        return view('dashboard.support.tickets', compact(
            'totalComplaints',
            'openComplaints',
            'closedComplaints',
            'complaintsList',
            'complaint',
            'complaintContext'
        ));
    }

    /**
     * Store a reply to a ticket.
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::findOrFail($id);

        SupportTicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $ticket->user_id,
            'admin_id' => auth('admin')->id(),
            'message' => $request->message,
            'is_admin_reply' => true,
        ]);

        // Update last_replied_at
        $ticket->update([
            'last_replied_at' => now(),
        ]);

        return redirect()->route('dashboard.support.tickets.show', $id)
            ->with('success', 'تم إرسال الرد بنجاح');
    }

    /**
     * Close a ticket.
     */
    public function close($id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $ticket->update([
            'status' => 'closed',
        ]);

        return redirect()->route('dashboard.support.tickets.show', $id)
            ->with('success', 'تم إغلاق التذكرة بنجاح');
    }

    /**
     * Map status to frontend status classes.
     */
    private function mapStatus($status)
    {
        return match ($status) {
            'open' => 'active',
            'in_progress' => 'warning',
            'resolved' => 'pending',
            'closed' => 'inactive',
            default => 'active',
        };
    }

    private function mapIndicator(SupportTicket $ticket)
    {
        if (($ticket->status ?? null) === 'closed') {
            return 'closed';
        }
        if (! empty($ticket->last_replied_at)) {
            return 'replied';
        }
        return 'open';
    }

    /**
     * Get initials from name.
     */
    private function getInitials($name)
    {
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1);
        }

        return mb_substr($name, 0, 2);
    }

    /**
     * Build complaint context for view without altering layout.
     */
    private function buildComplaintContext(SupportTicket $ticket)
    {
        $user = $ticket->user;
        $submitterRole = $ticket->submitted_by ?: (in_array(($user->user_type ?? null), ['landlord', 'both'], true) ? 'landlord' : 'tenant');
        $landlord = $ticket->landlord ?? null;
        $tenant = $ticket->tenant ?? null;
        $property = $ticket->property ?? null;
        $booking = $ticket->booking ?? null;

        if (! $landlord && ! $property) {
            try {
                if ($submitterRole === 'tenant') {
                    $booking = $booking ?: $user->bookings()->with(['property.user', 'user'])->latest()->first();
                    $property = $property ?: optional($booking)->property;
                    $landlord = $landlord ?: optional($property)->user;
                } else {
                    $booking = $booking ?: \App\Models\Booking::with(['property.user', 'user'])
                        ->whereHas('property', function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        })
                        ->latest()
                        ->first();
                    $property = $property ?: optional($booking)->property;
                    $landlord = $landlord ?: $user;
                    $tenant = $tenant ?: optional($booking)->user;
                }
            } catch (\Throwable $e) {
                
            }
        }

        // Derive damage compensation title
        $derivedTitle = $ticket->subject;
        try {
            $cat = (string) ($ticket->category ?? '');
            $desc = (string) ($ticket->description ?? '');
            $isDamage = mb_stripos($cat, 'اتلاف') !== false || mb_stripos($desc, 'اتلاف') !== false;
            if ($isDamage) {
                $match = null;
                if (preg_match('/اتلاف\s+([^\n\.،]+)/u', $desc, $m)) {
                    $match = trim($m[1]);
                }
                $derivedTitle = 'طلب تعويض عن اتلاف '.($match ?: 'ممتلكات');
            }
        } catch (\Throwable $e) {
            $derivedTitle = $ticket->subject;
        }

        return [
            'landlord_name' => $landlord?->name ?? ($submitterRole === 'landlord' ? ($user->name ?? null) : ($booking?->property?->user?->name ?? null)),
            'tenant_name' => $tenant?->name ?? ($submitterRole === 'tenant' ? ($user->name ?? null) : ($booking?->user?->name ?? null)),
            'property_title' => $property?->title ?? ($booking?->property?->title),
            'type' => $ticket->category,
            'submitter_role' => $submitterRole,
            'submitter_role_name' => $submitterRole === 'landlord' ? 'المؤجر' : 'المستأجر',
            'title' => $derivedTitle,
            'text' => $ticket->description,
        ];
    }
}

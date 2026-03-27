<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $submitterRole = $this->submitted_by ?: (in_array(($this->user?->user_type ?? null), ['landlord', 'both'], true) ? 'landlord' : 'tenant');
        $booking = $this->relationLoaded('booking') ? $this->booking : null;

        // Derive title: if category suggests damage/compensation, create a friendly title; else use subject
        $derivedTitle = $this->subject;
        try {
            $cat = (string) ($this->category ?? '');
            $desc = (string) ($this->description ?? '');
            $isDamage = mb_stripos($cat, 'اتلاف') !== false || mb_stripos($desc, 'اتلاف') !== false;
            if ($isDamage) {
                $match = null;
                if (preg_match('/اتلاف\s+([^\n\.،]+)/u', $desc, $m)) {
                    $match = trim($m[1]);
                }
                $derivedTitle = 'طلب تعويض عن اتلاف '.($match ?: 'ممتلكات');
            }
        } catch (\Throwable $e) {
            $derivedTitle = $this->subject;
        }

        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'category' => $this->category,
            'assigned_to' => $this->assigned_to,
            'last_replied_at' => optional($this->last_replied_at)->toIso8601String(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),

            'type' => $this->category,
            'submitter_role' => $submitterRole,
            'submitter_role_name' => $submitterRole === 'landlord' ? 'المؤجر' : 'المستأجر',
            'title' => $derivedTitle,

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user?->id,
                    'name' => $this->user?->name,
                    'email' => $this->user?->email,
                    'avatar' => $this->user?->avatar,
                ];
            }),

            'assigned' => $this->whenLoaded('assignedTo', function () {
                return [
                    'id' => $this->assignedTo?->id,
                    'name' => $this->assignedTo?->name,
                    'email' => $this->assignedTo?->email,
                ];
            }),

            'tenant' => $this->whenLoaded('tenant', function () {
                return [
                    'id' => $this->tenant?->id,
                    'name' => $this->tenant?->name,
                    'email' => $this->tenant?->email,
                ];
            }, function () use ($submitterRole, $booking) {
                if ($submitterRole === 'tenant') {
                    return [
                        'id' => $this->user?->id,
                        'name' => $this->user?->name,
                        'email' => $this->user?->email,
                    ];
                }
                return $booking ? [
                    'id' => $booking->user?->id,
                    'name' => $booking->user?->name,
                    'email' => $booking->user?->email,
                ] : null;
            }),

            'landlord' => $this->whenLoaded('landlord', function () {
                return [
                    'id' => $this->landlord?->id,
                    'name' => $this->landlord?->name,
                    'email' => $this->landlord?->email,
                ];
            }, function () use ($submitterRole, $booking) {
                if ($submitterRole === 'landlord') {
                    return [
                        'id' => $this->user?->id,
                        'name' => $this->user?->name,
                        'email' => $this->user?->email,
                    ];
                }
                return $booking ? [
                    'id' => $booking->property?->user?->id,
                    'name' => $booking->property?->user?->name,
                    'email' => $booking->property?->user?->email,
                ] : null;
            }),

            'property' => $this->whenLoaded('property', function () {
                return [
                    'id' => $this->property?->id,
                    'title' => $this->property?->title,
                    'city' => $this->property?->city,
                ];
            }, function () use ($booking) {
                return $booking ? [
                    'id' => $booking->property?->id,
                    'title' => $booking->property?->title,
                    'city' => $booking->property?->city,
                ] : null;
            }),

            'replies' => $this->whenLoaded('replies', function () {
                return $this->replies->map(function ($reply) {
                    $authorName = $reply->is_admin_reply ? ($reply->admin?->name) : ($reply->user?->name);
                    $authorType = $reply->is_admin_reply ? 'admin' : 'user';

                    return [
                        'id' => $reply->id,
                        'message' => $reply->message,
                        'is_admin_reply' => (bool) $reply->is_admin_reply,
                        'author' => [
                            'type' => $authorType,
                            'name' => $authorName,
                        ],
                        'created_at' => optional($reply->created_at)->toIso8601String(),
                    ];
                });
            }),
        ];
    }
}
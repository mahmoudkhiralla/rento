<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $property = $this->property; // may be null
        $landlord = $property?->user;

        // حساب الأيام والقيمة الإجمالية إن توفرت بيانات العقار
        $days = null;
        $total = null;
        if ($this->start_date && $this->end_date) {
            try {
                $days = Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date));
                $days = max(1, $days);
            } catch (\Throwable $e) {
                $days = null;
            }
        }
        if ($property && $property->price && $days) {
            $total = round((float) $property->price * $days, 2);
        }

        return [
            'id' => $this->id,
            'status' => $this->status,
            'start_date' => $this->start_date ? (string) Carbon::parse($this->start_date)->format('Y-m-d') : null,
            'end_date' => $this->end_date ? (string) Carbon::parse($this->end_date)->format('Y-m-d') : null,
            'guests' => $this->guests,
            'days' => $days,
            'total_price' => $total,

            'tenant' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
                'avatar' => $this->user?->avatar,
            ],

            'property' => [
                'id' => $property?->id,
                'title' => $property?->title,
                'city' => $property?->city,
                'image' => $property?->image,
                'price' => $property?->price,
                'landlord_id' => $landlord?->id,
                'landlord_name' => $landlord?->name,
                'landlord_email' => $landlord?->email,
                'landlord_avatar' => $landlord?->avatar,
            ],

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}

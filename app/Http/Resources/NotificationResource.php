<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'user_id'      => $this->user_id,
            'title'        => $this->title,
            'message'      => $this->message,
            'read_at'      => $this->read_at,
            'metadata'     => $this->metadata,
            'notificationable_id'   => $this->notificationable_id,
            'notificationable_type' => $this->notificationable_type,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),

            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}

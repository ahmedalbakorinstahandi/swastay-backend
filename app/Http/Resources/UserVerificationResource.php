<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserVerificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'user_id'      => $this->user_id,
            'file_path'    => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'type'         => $this->type,
            'status'       => $this->status,
            'reviewed_by'  => $this->reviewed_by,
            'reviewed_at'  => $this->reviewed_at,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),

            'user'     => new UserResource($this->whenLoaded('user')),
            'reviewer' => new UserResource($this->whenLoaded('reviewer')),
        ];
    }
}

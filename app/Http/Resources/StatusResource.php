<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'statusable_id'     => $this->statusable_id,
            'statusable_type'   => $this->statusable_type,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

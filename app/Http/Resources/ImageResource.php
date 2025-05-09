<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'path'           => $this->path,
            'type'           => $this->type,
            'url'            => $this->url,
            'imageable_id'   => $this->imageable_id,
            'imageable_type' => $this->imageable_type,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

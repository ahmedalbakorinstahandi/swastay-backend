<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'key'         => $this->key,
            'value'       => $this->value,
            'type'        => $this->type,
            'allow_null'  => $this->allow_null,
            'is_settings' => $this->is_settings,
        ];
    }
}

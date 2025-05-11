<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingRuleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'allows_pets' => $this->allows_pets,
            'allows_smoking' => $this->allows_smoking,
            'allows_parties' => $this->allows_parties,
            'allows_children' => $this->allows_children,
            'remove_shoes' => $this->remove_shoes,
            'no_extra_guests' => $this->no_extra_guests,

            'quiet_hours' => $this->quiet_hours,
            'restricted_rooms_note' => $this->restricted_rooms_note,
            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,
            'garbage_disposal_note' => $this->garbage_disposal_note,
            'pool_usage_note' => $this->pool_usage_note,
            'forbidden_activities_note' => $this->forbidden_activities_note,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

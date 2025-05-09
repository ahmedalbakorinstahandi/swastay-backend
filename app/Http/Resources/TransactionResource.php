<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'amount'          => $this->amount,
            'description'     => $this->description,
            'status'          => $this->status,
            'type'            => $this->type,
            'direction'       => $this->direction,
            'method'          => $this->method,
            'transactionable_id'   => $this->transactionable_id,
            'transactionable_type' => $this->transactionable_type,
            'attached'        => $this->attached,
            'attached_url'    => $this->attached_url,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),

            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}

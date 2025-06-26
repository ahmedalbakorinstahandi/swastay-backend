<?php

namespace App\Http\Requests\Booking;

use App\Http\Requests\BaseFormRequest;
use App\Models\User;

class UpdateRequest extends BaseFormRequest
{

    public function rules(): array
    {

        $user = User::auth();

        if ($user->isAdmin()) {
            return [
                'status' => 'nullable|in:accepted,confirmed,completed,cancelled,rejected',
                'admin_notes' => 'nullable|string',
            ];
        }

        return [
            'status' => 'nullable|in:completed',
            'host_notes' => 'nullable|string',
        ];
    }
}


 

// $table->enum('status', ['pending', 'accepted', 'confirmed', 'completed', 'cancelled', 'rejected']);

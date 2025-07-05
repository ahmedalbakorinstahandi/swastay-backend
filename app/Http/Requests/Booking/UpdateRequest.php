<?php

namespace App\Http\Requests\Booking;

use App\Http\Requests\BaseFormRequest;
use App\Models\Booking;
use App\Models\Listing;
use App\Models\User;
use App\Services\MessageService;

class UpdateRequest extends BaseFormRequest
{

    public function rules(): array
    {

        $id = $this->route('id');

        $booking = Booking::find($id);

        $user = User::auth();


        if (!$user) {
            MessageService::abort(403, 'messages.permission.error');
        }


        if (!$booking) {
            MessageService::abort(404, 'messages.booking.not_found');
        }



        if ($user->isAdmin()) {
            // 'status' => 'nullable|in:accepted,confirmed,completed,cancelled,rejected',
            $adminRules = [
                'admin_notes' => 'nullable|string',
            ];


            if ($booking->status == 'pending') {
                $adminRules['status'] = 'nullable|in:accepted,rejected';
            }

            if ($booking->status == 'accepted') {
                $adminRules['status'] = 'nullable|in:confirmed,cancelled';
            }

            if ($booking->status == 'confirmed') {
                $adminRules['status'] = 'nullable|in:completed,cancelled';
            }

            return $adminRules;
        }


        // 'status' => 'nullable|in:accepted,completed,rejected',
        $hostRules = [
            'host_notes' => 'nullable|string',
        ];


        if ($booking->status == 'pending') {
            $hostRules['status'] = 'nullable|in:accepted,rejected';
        }

        if ($booking->status == 'confirmed') {
            $hostRules['status'] = 'nullable|in:completed';
        }

        return $hostRules;
    }
}


 

// $table->enum('status', ['pending', 'accepted', 'confirmed', 'completed', 'cancelled', 'rejected']);

<?php

namespace App\Http\Requests\Notification;

use App\Http\Requests\BaseFormRequest;

class SendNotificationToAllUsersRequest extends BaseFormRequest
{
    
    public function rules(): array
    {
        return [
            'title'                 => 'required|string|max:255',
            'message'               => 'required|string',
        ];
    }
}

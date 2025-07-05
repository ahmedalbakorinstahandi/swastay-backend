<?php

namespace App\Http\Requests\Transaction;

use App\Http\Requests\BaseFormRequest;

class UpdateTransactionRequest extends BaseFormRequest
{
     
    public function rules(): array
    {
        return [
            'status' => 'nullable|in:completed,failed,refund',
            'admin_notes' => 'nullable|string',
        ];
    }
}

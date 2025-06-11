<?php

namespace App\Http\Requests\Booking;

use App\Http\Requests\BaseFormRequest;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AddTransactionRequest extends BaseFormRequest
{
    public function rules(): array
    {


        $rules = [
            'amount' => 'required|numeric|min:0',
            'method' => 'required|string|in:wallet,shamcash,alharam,cash',
            'attached' => 'nullable|string|max:100',
        ];

        return $rules;
    }
}

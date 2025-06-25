<?php

namespace App\Http\Requests\Listing;

use App\Http\Requests\BaseFormRequest;
use App\Models\Listing;
use App\Models\User;
use App\Services\LanguageService;
use Illuminate\Foundation\Http\FormRequest;

class ReOrderListingRequest extends BaseFormRequest
{

    public function rules(): array
    {

        $min = 1;
        $max = Listing::all()->count();


        $rules = [
            'listing_index' => 'required|integer|min:' . $min . '|max:' . $max,
        ];

        return $rules;
    }
}

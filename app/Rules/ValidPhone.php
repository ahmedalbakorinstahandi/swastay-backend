<?php

namespace App\Rules;

use libphonenumber\PhoneNumberUtil;
use Illuminate\Contracts\Validation\Rule;

class ValidPhone implements Rule
{
    public function passes($attribute, $value)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            // null لتفسير الرقم بشكل عالمي
            $numberProto = $phoneUtil->parse($value, null);
            return $phoneUtil->isValidNumber($numberProto);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function message()
    {
        return 'رقم الهاتف غير صالح.';
    }
}

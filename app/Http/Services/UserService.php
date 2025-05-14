<?php

namespace App\Http\Services;

use App\Models\User;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Services\PhoneService;
use App\Services\WhatsappMessageService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class UserService
{
    public function index($filters = [])
    {
        return FilterService::applyFilters(
            User::query(),
            $filters,
            [['first_name', 'last_name'], 'email', 'phone_number'],
            ['wallet_balance'],
            [],
            ['role', 'status', 'id_verified', 'is_verified'],
            ['role', 'status']
        );
    }

    public function show($id)
    {
        $user = User::where('id', $id)->first();

        if (!$user) {
            MessageService::abort(404, 'messages.user.not_found');
        }

        return $user;
    }

    public function create($data)
    {
        $data['password'] = Hash::make($data['password']);


        $phoneParts = PhoneService::parsePhoneParts($data['phone']);

        $data['country_code'] = $phoneParts['country_code'];
        $data['phone_number'] = $phoneParts['national_number'];
        $data['phone_verified'] = true;
        $data['is_verified'] = true;

        if (empty($data['email'])) {
            $data['email'] = '';
        } else {
            $data['email_verified'] = true;
        }

        $data['wallet_balance'] = 0;
        $data['role'] = 'user';
        $data['status'] = 'active';

        // if ($data['id_verified'] == 'none') {
        //     unset($data['bank_details']);
        // }

        $user = User::create($data);



        $message1 = "مرحباً {$user->first_name} {$user->last_name}، نحن سعداء بانضمامك إلينا! لقد تم إنشاء حسابك بنجاح ويمكنك الآن الاستفادة من خدماتنا. إذا كنت بحاجة إلى أي مساعدة، لا تتردد في التواصل معنا. يمكنك زيارة موقعنا على الرابط التالي: https://sawastay.com/";
        
        $phone = $user->country_code . $user->phone_number;
        
        WhatsappMessageService::send($phone, $message1);


        $message2 = "مرحبًا بك في SawaStay!\n\nشكرًا لانضمامك إلى عائلة SawaStay!\nيسعدنا أن نعلن بأن موعد الانطلاق الرسمي لمنصتنا سيكون في 1 حزيران 2025، ونتطلع إلى تقديم تجربة استثنائية للمضيفين والضيوف على حد سواء.\n\nنحن هنا لدعمك في كل خطوة من خطوات رحلتك كمضيف. إذا كان لديك أي استفسار أو تحتاج إلى مساعدة، لا تتردد في التواصل معنا عبر:\n\nالبريد الإلكتروني: contact@sawastay.com\nرقم الهاتف: +963 935 919 671\n\nمع أطيب التحيات،\nفريق SawaStay";

        WhatsappMessageService::send($phone, $message2);


        return $user;
    }

    public function update(User $user, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if (isset($data['phone'])) {
            $phoneParts = PhoneService::parsePhoneParts($data['phone']);

            $data['country_code'] = $phoneParts['country_code'];
            $data['phone_number'] = $phoneParts['national_number'];
        }

        if (isset($data['email'])) {
            $email_exists = User::where('email', $data['email'])->first();

            if ($email_exists && $email_exists->id != $user->id) {
                MessageService::abort(422, 'messages.user.email_exists');
            }

            $data['email_verified'] = true;
        }

        // if ($user->id_verified == 'none') {
        //     unset($data['bank_details']);
        // }

        $user->update($data);

        return $user;
    }

    public function destroy(User $user)
    {
        $user->delete();
    }


    public function updateProfile(User $user, array $data)
    {

        if (isset($data['old_password'])) {
            if (!Hash::check($data['old_password'], $user->password)) {
                MessageService::abort(422, 'messages.user.old_password');
            } else {
                unset($data['old_password']);
                if (isset($data['password'])) {
                    $data['password'] = Hash::make($data['password']);
                }
            }
        }

        // if ($user->id_verified == 'none') {
        //     unset($data['bank_details']);
        // }


        $user->update($data);

        return $user;
    }
}

<?php

namespace App\Http\Services;

use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Services\WhatsappMessageService;

class TransactionService
{
    public function index($data)
    {
        // with user
        $query = Transaction::query()->with('user');

        $user = User::auth();

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }


        $query = FilterService::applyFilters(
            $query,
            $data,
            ['description'],
            ['amount'],
            ['created_at'],
            ['status', 'direction', 'method'],
            ['direction', 'status', 'method'],
        );

        return $query;
    }


    public function show($id)
    {

        $transaction = Transaction::where('id', $id)->with('user')->first();

        if (!$transaction) {
            MessageService::abort(404, 'messages.transaction.not_found');
        }

        return $transaction;
    }

    public function update($transaction, $data)
    {
        $transaction->update($data);


        $transaction->load('user');

        return $transaction;
    }


    public function destroy($transaction)
    {
        $transaction->delete();
    }


    public function sendWesternUnionDetails()
    {

        $westernUnionDetails = Setting::where('key', 'western_union')->first()->value;

        if (!$westernUnionDetails) {
            MessageService::abort(404, 'messages.setting.not_found');
        }

        $user = User::auth();

        $message = "messages.transaction.western_union.details";

        $fullPhone = $user->country_code . $user->phone_number;

        WhatsappMessageService::send(
            $fullPhone,
            __(
                $message,
                [
                    'name' => $user->first_name,
                    'western_union_details' => $westernUnionDetails
                ],
                $user->language,
            )
        );
    }
}

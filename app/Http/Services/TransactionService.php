<?php

namespace App\Http\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Services\FilterService;
use App\Services\MessageService;

class TransactionService
{
    public function index($data)
    {
        $query = Transaction::query();

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

        $transaction = Transaction::where('id', $id)->first();

        if (!$transaction) {
            MessageService::abort(404, 'messages.transaction.not_found');
        }

        return $transaction;
    }

    public function update($transaction, $data) {}


    public function destroy($transaction)
    {
        $transaction->delete();
    }
}

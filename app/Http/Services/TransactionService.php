<?php

namespace App\Http\Services;

use App\Models\Transaction;
use App\Services\FilterService;
use App\Services\MessageService;

class TransactionService
{
    public function index($data)
    {
        $query = Transaction::query();

        $query = FilterService::applyFilters(
            $query,
            $data,
            ['description'],
            ['amount'],
            ['created_at'],
            ['status', 'direction'],
            ['direction', 'status'],
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

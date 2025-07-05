<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Services\TransactionService;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }


    public function index()
    {
        $data = request()->all();

        $transactions = $this->transactionService->index($data);

        return ResponseService::response([
            'success' => true,
            'data' => $transactions,
            'resource' => TransactionResource::class,
            'meta' => true,
            'status' => 200,
        ]);
    }

    public function show($id)
    {
        $transaction = $this->transactionService->show($id);

        return ResponseService::response([
            'success' => true,
            'data' => $transaction,
        ]);
    }

    public function update(UpdateTransactionRequest $request, $id)
    {

        $transaction = $this->transactionService->show($id);

        $data = $request->validated();

        $transaction = $this->transactionService->update($transaction, $data);

        return ResponseService::response([
            'success' => true,
            'data' => $transaction,
            'resource' => TransactionResource::class,
            'status' => 200,
        ]);
    }

    // send to me by whatsapp western union details
    public function sendWesternUnionDetails()
    {
        $this->transactionService->sendWesternUnionDetails();

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.transaction.western_union.sent',
            'status' => 200,
        ]);
    }
}

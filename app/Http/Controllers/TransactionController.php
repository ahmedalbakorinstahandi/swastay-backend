<?php

namespace App\Http\Controllers;

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
}

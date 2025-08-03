<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;

class DigiBankarWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('DigiBankar Webhook Payload:', $request->all());

        $eventType = $request->input('EventType');
        $data = $request->input('Data', []);

        if ($eventType === 'PaymentRequestStatusChanged') {
            $newStatus = strtolower($data['NewStatus'] ?? '');
            $oldStatus = strtolower($data['OldStatus'] ?? '');
            $orderId   = $data['OrderId'] ?? null;

            Log::info("PaymentRequestStatusChanged: OrderId={$orderId}, OldStatus={$oldStatus}, NewStatus={$newStatus}");

            $successStatuses = ['paid', 'confirmed', 'success'];
            $failedStatuses  = ['canceled', 'deleted', 'failed'];

            if (in_array($newStatus, $successStatuses)) {
                Transaction::where('id', $orderId)
                    ->update(['status' => 'completed']);

                Log::info("Transaction {$orderId} marked as completed.");

                // TODO : send notification to user

                // TODO : send notification to admin
            } elseif (in_array($newStatus, $failedStatuses)) {
                Transaction::where('id', $orderId)
                    ->update(['status' => 'failed']);

                Log::warning("Transaction {$orderId} marked as failed.");

                // TODO : send notification to user

                // TODO : send notification to admin
            }
        }

        return response()->json(['message' => 'ok']);
    }
}

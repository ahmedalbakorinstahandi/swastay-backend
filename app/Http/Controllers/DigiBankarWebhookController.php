<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use PDO;

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

            if ($newStatus != 'Completed') {
                Log::info("Transaction {$orderId} status: {$newStatus}, Completed: " . ($newStatus === 'Completed' ? 'true' : 'false'));
                return response()->json(['message' => 'Transaction not completed'], 200);
            }

            Log::info("PaymentRequestStatusChanged: OrderId={$orderId}, OldStatus={$oldStatus}, NewStatus={$newStatus}");

            $failedStatuses  = ['canceled', 'deleted', 'failed'];

            Log::info("Transaction {$orderId} status: {$newStatus}, Completed: " . ($newStatus === 'Completed' ? 'true' : 'false'));

            if ($newStatus === 'Completed') {
                $transaction = Transaction::where('id', $orderId)->first();

                if (!$transaction) {
                    Log::warning("Transaction {$orderId} not found.");
                    return response()->json(['message' => 'Transaction not found'], 404);
                }

                $transaction->update(['status' => 'completed']);

                $booking = Booking::find($transaction->transactionable_id);

                $booking->update(['status' => 'confirmed']);

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

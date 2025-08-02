<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DigiBankarWebhookController extends Model
{
    public function handle(Request $request)
    {
        Log::info('DigiBankar Webhook:', $request->all());

        $status = $request->input('status');
        $orderId = $request->input('orderId');
        $requestId = $request->input('requestId');

        // Example: update your booking/order
        if ($status === 'SUCCESS') {
            // mark as paid
            // Order::where('order_id', $orderId)->update(['status' => 'paid']);
        } elseif ($status === 'FAILED') {
            // handle failed
        }

        return response()->json(['message' => 'ok']);
    }
}

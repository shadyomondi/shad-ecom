<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Mock M-Pesa payment callback
     * In production, this would be called by M-Pesa API
     */
    public function mpesaCallback(Request $request)
    {
        try {
            // Validate the callback (in production, verify M-Pesa signature)
            $orderId = $request->input('order_id');
            $transactionId = $request->input('transaction_id');
            $amount = $request->input('amount');
            $status = $request->input('status', 'success'); // success or failed

            Log::info('M-Pesa Callback received', [
                'order_id' => $orderId,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'status' => $status
            ]);

            $order = Order::findOrFail($orderId);

            if ($status === 'success') {
                DB::transaction(function () use ($order, $transactionId) {
                    // Update order status
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'paid',
                    ]);

                    // Reduce stock for each product in the order
                    foreach ($order->items as $item) {
                        $product = Product::find($item->product_id);

                        if ($product) {
                            $product->decrement('stock', $item->quantity);

                            Log::info('Stock reduced', [
                                'product_id' => $product->id,
                                'quantity_reduced' => $item->quantity,
                                'remaining_stock' => $product->stock
                            ]);
                        }
                    }
                });

                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully'
                ]);
            } else {
                // Payment failed
                $order->update([
                    'payment_status' => 'failed',
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error processing payment'
            ], 500);
        }
    }

    /**
     * Simulate payment for testing purposes
     */
    public function simulatePayment(Order $order)
    {
        // Ensure user is authenticated
        if (Auth::id() === null) {
            abort(401, 'Unauthenticated');
        }

        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Simulate successful payment
        $this->mpesaCallback(new Request([
            'order_id' => $order->id,
            'transaction_id' => 'MPESA-' . strtoupper(uniqid()),
            'amount' => $order->total,
            'status' => 'success'
        ]));

        return redirect()->route('orders.show', $order)
            ->with('success', 'Payment successful! Your order has been confirmed.');
    }
}

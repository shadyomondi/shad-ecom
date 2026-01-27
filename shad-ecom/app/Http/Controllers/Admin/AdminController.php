<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get low stock products (stock <= 10)
        $lowStockProducts = Product::with('category')
            ->where('stock', '<=', 10)
            ->orderBy('stock', 'asc')
            ->get();

        // Get pending payment orders
        $pendingOrders = Order::with(['user', 'items'])
            ->where('payment_status', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        // Get recent orders for stats
        $recentOrders = Order::latest()->limit(5)->get();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $pendingPayments = Order::where('payment_status', 'pending')->count();
        $totalProducts = Product::count();

        return view('admin.dashboard', compact(
            'lowStockProducts',
            'pendingOrders',
            'recentOrders',
            'totalRevenue',
            'pendingPayments',
            'totalProducts'
        ));
    }

    public function confirmPayment(Order $order)
    {
        if ($order->payment_status === 'paid') {
            return back()->with('error', 'This order has already been paid.');
        }

        // Simulate M-Pesa callback
        $request = new Request([
            'order_id' => $order->id,
            'transaction_id' => 'ADMIN-MOCK-' . strtoupper(uniqid()),
            'amount' => $order->total,
            'status' => 'success'
        ]);

        app(\App\Http\Controllers\PaymentController::class)->mpesaCallback($request);

        return back()->with('success', "Payment confirmed for order {$order->order_number}!");
    }

    public function getOrderStatus(Order $order)
    {
        return response()->json([
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'updated_at' => $order->updated_at->toIso8601String(),
        ]);
    }
}

<?php
// Quick test to see what dashboard data returns
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DASHBOARD DATA TEST ===\n\n";

$totalProducts = \App\Models\Product::count();
echo "Total Products: {$totalProducts}\n";

$lowStockCount = \App\Models\Product::where('stock', '<=', 10)->count();
echo "Low Stock Products: {$lowStockCount}\n";

$totalRevenue = \App\Models\Order::where('payment_status', 'paid')->sum('total');
echo "Total Revenue: KSh " . number_format($totalRevenue, 2) . "\n";

$pendingPayments = \App\Models\Order::where('payment_status', 'pending')->count();
echo "Pending Payments: {$pendingPayments}\n";

$totalOrders = \App\Models\Order::count();
echo "Total Orders: {$totalOrders}\n";

echo "\n=== LOW STOCK PRODUCTS ===\n";
$lowStock = \App\Models\Product::with('category')->where('stock', '<=', 10)->get();
foreach ($lowStock as $product) {
    echo "- {$product->name}: {$product->stock} units (Category: " . ($product->category ? $product->category->name : 'N/A') . ")\n";
}

echo "\n=== ORDERS ===\n";
$orders = \App\Models\Order::with('user')->get();
foreach ($orders as $order) {
    echo "- Order #{$order->order_number}: KSh " . number_format($order->total, 2) . " - {$order->payment_status}\n";
}

echo "\nTest complete!\n";

@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-background-light dark:bg-background-dark py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Message -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 dark:bg-green-900/30 mb-4">
                <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-4xl">
                    check_circle
                </span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Order Placed Successfully!</h1>
            <p class="text-gray-600 dark:text-gray-400">Thank you for your order. We've received it and will process it soon.</p>
        </div>

        <!-- Order Details Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Order Details</h2>
            </div>

            <div class="p-6 space-y-4">
                <!-- Order Number -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Order Number</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $order->order_number }}</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' :
                           'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>

                <!-- Order Date -->
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-gray-400">Order Date</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                </div>

                <!-- Shipping Address -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Shipping Address</h3>
                    <div class="text-gray-600 dark:text-gray-400 space-y-1">
                        <p>{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_city }}, {{ $order->shipping_zip }}</p>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Order Items</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex items-center gap-4">
                            @if($item->product->image)
                            <img src="{{ asset('storage/' . $item->product->image) }}"
                                 alt="{{ $item->product->name }}"
                                 class="w-16 h-16 object-cover rounded-lg">
                            @else
                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-gray-400">
                                    image
                                </span>
                            </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $item->product->name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Qty: {{ $item->quantity }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    KSh {{ number_format($item->price * $item->quantity, 2) }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal</span>
                        <span>KSh {{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Tax</span>
                        <span>KSh {{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-semibold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span>Total</span>
                        <span>KSh {{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Notice -->
        @if($order->payment_status === 'pending')
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400 text-xl">
                    info
                </span>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-400 mb-1">Payment Pending</h3>
                    <p class="text-sm text-yellow-700 dark:text-yellow-500">
                        Please complete the M-Pesa payment to confirm your order. You will receive a prompt on your phone shortly.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('orders.show', $order) }}"
               class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-xl mr-2">
                    visibility
                </span>
                Track Order
            </a>
            <a href="{{ route('shop.index') }}"
               class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <span class="material-symbols-outlined text-xl mr-2">
                    shopping_bag
                </span>
                Continue Shopping
            </a>
        </div>

        <!-- Additional Info -->
        <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
            <p>A confirmation email has been sent to your registered email address.</p>
            <p class="mt-2">Need help? <a href="#" class="text-primary hover:underline">Contact Support</a></p>
        </div>
    </div>
</div>
@endsection

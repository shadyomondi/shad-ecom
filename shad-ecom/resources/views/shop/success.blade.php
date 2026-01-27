<x-app-layout>
    <div class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Placed Successfully!</h1>
                <p class="text-gray-600 mb-8">Thank you for your purchase. Your order has been received.</p>

                <!-- Order Details -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <div class="grid grid-cols-2 gap-4 text-left">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Order Number</p>
                            <p class="text-lg font-bold text-gray-900">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Order Total</p>
                            <p class="text-lg font-bold text-gray-900">${{ number_format($order->total, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Payment Status</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Order Status</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- M-Pesa Payment Prompt -->
                @if($order->payment_status === 'pending')
                    <div class="bg-orange-50 border-2 border-orange-200 rounded-lg p-6 mb-8">
                        <div class="flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-orange-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            <h3 class="text-xl font-bold text-orange-900">Complete Payment via M-Pesa</h3>
                        </div>
                        <p class="text-gray-700 mb-4">To complete your order, please pay via M-Pesa:</p>
                        <div class="bg-white rounded p-4 mb-4">
                            <ol class="text-left space-y-2 text-sm text-gray-700">
                                <li>1. Go to M-Pesa on your phone</li>
                                <li>2. Select Lipa Na M-Pesa</li>
                                <li>3. Select Pay Bill</li>
                                <li>4. Enter Business Number: <strong class="text-gray-900">123456</strong></li>
                                <li>5. Enter Account Number: <strong class="text-gray-900">{{ $order->order_number }}</strong></li>
                                <li>6. Enter Amount: <strong class="text-gray-900">KES {{ number_format($order->total * 130, 2) }}</strong></li>
                                <li>7. Enter your M-Pesa PIN and confirm</li>
                            </ol>
                        </div>
                        <p class="text-xs text-gray-600">You will receive an SMS confirmation from M-Pesa once payment is complete.</p>

                        <!-- Simulate Payment Button (for testing) -->
                        <form action="{{ route('payment.simulate', $order) }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium">
                                Simulate M-Pesa Payment (Test Only)
                            </button>
                        </form>
                    </div>
                @else
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6 mb-8">
                        <p class="text-green-800 font-semibold">Payment Confirmed! Your order is being processed.</p>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('orders.show', $order) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium">
                        View Order Details
                    </a>
                    <a href="{{ route('shop.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium">
                        Continue Shopping
                    </a>
                </div>
            </div>

            <!-- What's Next -->
            <div class="mt-8 bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">What Happens Next?</h2>
                <div class="space-y-3 text-gray-600">
                    <div class="flex items-start">
                        <svg class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p>Once payment is confirmed, we'll start processing your order</p>
                    </div>
                    <div class="flex items-start">
                        <svg class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p>You'll receive an email confirmation with your order details</p>
                    </div>
                    <div class="flex items-start">
                        <svg class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p>Track your order status from your orders page</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

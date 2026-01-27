<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Details') }} - {{ $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Auto-refresh notification -->
            @if($order->payment_status !== 'paid')
                <div id="refresh-notice" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="animate-spin h-5 w-5 mr-3 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Tracking payment status... Auto-refreshing every 5 seconds</span>
                    </div>
                    <button onclick="stopAutoRefresh()" class="text-blue-700 hover:text-blue-900 font-medium text-sm">Stop</button>
                </div>
            @endif

            <!-- Order Status -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" id="order-status-card">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $order->order_number }}</h3>
                            <p class="text-gray-600">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
                        </div>
                        <div class="text-right">
                            <div class="flex gap-2 mb-2">
                                <span id="payment-status-badge" class="px-3 py-1 text-sm font-semibold rounded
                                    {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    Payment: <span id="payment-status-text">{{ ucfirst($order->payment_status) }}</span>
                                </span>
                                <span id="order-status-badge" class="px-3 py-1 text-sm font-semibold rounded bg-blue-100 text-blue-800">
                                    Status: <span id="order-status-text">{{ ucfirst($order->status) }}</span>
                                </span>
                            </div>
                            @if($order->payment_status === 'paid')
                                <a href="{{ route('orders.invoice', $order) }}"
                                   id="invoice-link"
                                   class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded font-medium text-sm">
                                    Download Invoice
                                </a>
                            @else
                                <div id="invoice-link"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h4 class="font-semibold text-lg mb-3">Shipping Address</h4>
                    <p class="text-gray-700">{{ $order->shipping_address }}</p>
                    <p class="text-gray-700">{{ $order->shipping_city }}, {{ $order->shipping_zip }}</p>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h4 class="font-semibold text-lg mb-4">Order Items</h4>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center gap-4 pb-4 border-b last:border-b-0">
                                <div class="w-20 h-20 flex-shrink-0">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}"
                                             alt="{{ $item->product->name }}"
                                             class="w-full h-full object-cover rounded">
                                    @else
                                        <div class="w-full h-full bg-gray-200 rounded"></div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h5 class="font-semibold text-gray-900">
                                        @if($item->product)
                                            {{ $item->product->name }}
                                        @else
                                            Product (Deleted)
                                        @endif
                                    </h5>
                                    <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                    <p class="text-sm text-gray-600">Price: KSh {{ number_format($item->price, 2) }} each</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-lg text-gray-900">KSh {{ number_format($item->price * $item->quantity, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="font-semibold text-lg mb-4">Order Summary</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>KSh {{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax</span>
                            <span>KSh {{ number_format($order->tax, 2) }}</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between text-xl font-bold">
                            <span>Total</span>
                            <span>KSh {{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    ← Back to Orders
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let autoRefreshInterval;
        const orderId = {{ $order->id }};
        const orderNumber = '{{ $order->order_number }}';

        function checkOrderStatus() {
            fetch(`/api/orders/${orderId}/status`)
                .then(response => response.json())
                .then(data => {
                    // Update payment status
                    const paymentStatusText = document.getElementById('payment-status-text');
                    const paymentStatusBadge = document.getElementById('payment-status-badge');
                    const orderStatusText = document.getElementById('order-status-text');
                    const orderStatusBadge = document.getElementById('order-status-badge');

                    if (paymentStatusText) {
                        paymentStatusText.textContent = data.payment_status.charAt(0).toUpperCase() + data.payment_status.slice(1);
                    }

                    if (orderStatusText) {
                        orderStatusText.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    }

                    // Update badge colors
                    if (paymentStatusBadge) {
                        if (data.payment_status === 'paid') {
                            paymentStatusBadge.className = 'px-3 py-1 text-sm font-semibold rounded bg-green-100 text-green-800';
                        } else {
                            paymentStatusBadge.className = 'px-3 py-1 text-sm font-semibold rounded bg-yellow-100 text-yellow-800';
                        }
                    }

                    // If payment is confirmed, show invoice link and stop auto-refresh
                    if (data.payment_status === 'paid') {
                        const invoiceLink = document.getElementById('invoice-link');
                        if (invoiceLink && !invoiceLink.querySelector('a')) {
                            invoiceLink.innerHTML = `
                                <a href="/orders/${orderId}/invoice"
                                   class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded font-medium text-sm">
                                    Download Invoice
                                </a>
                            `;
                        }

                        // Hide refresh notice
                        const refreshNotice = document.getElementById('refresh-notice');
                        if (refreshNotice) {
                            refreshNotice.style.display = 'none';
                        }

                        // Show success message
                        const orderStatusCard = document.getElementById('order-status-card');
                        if (orderStatusCard && !document.getElementById('payment-confirmed-notice')) {
                            const successNotice = document.createElement('div');
                            successNotice.id = 'payment-confirmed-notice';
                            successNotice.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center';
                            successNotice.innerHTML = `
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Payment confirmed! Your order is being processed.</span>
                            `;
                            orderStatusCard.parentNode.insertBefore(successNotice, orderStatusCard);
                        }

                        stopAutoRefresh();
                    }
                })
                .catch(error => {
                    console.error('Error checking order status:', error);
                });
        }

        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                const refreshNotice = document.getElementById('refresh-notice');
                if (refreshNotice) {
                    refreshNotice.style.display = 'none';
                }
            }
        }

        // Start auto-refresh if payment is not yet confirmed
        @if($order->payment_status !== 'paid')
            autoRefreshInterval = setInterval(checkOrderStatus, 5000); // Check every 5 seconds

            // Stop auto-refresh after 10 minutes to avoid indefinite checking
            setTimeout(stopAutoRefresh, 600000);
        @endif
    </script>
    @endpush
</x-app-layout>

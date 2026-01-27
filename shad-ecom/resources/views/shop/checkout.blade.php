<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Shipping Information -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow p-6 mb-6">
                            <h2 class="text-xl font-semibold mb-4">Shipping Information</h2>

                            <div class="space-y-4">
                                <div>
                                    <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                                    <input type="text"
                                           name="shipping_address"
                                           id="shipping_address"
                                           value="{{ old('shipping_address') }}"
                                           required
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('shipping_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                        <input type="text"
                                               name="shipping_city"
                                               id="shipping_city"
                                               value="{{ old('shipping_city') }}"
                                               required
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('shipping_city')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="shipping_zip" class="block text-sm font-medium text-gray-700 mb-1">ZIP Code</label>
                                        <input type="text"
                                               name="shipping_zip"
                                               id="shipping_zip"
                                               value="{{ old('shipping_zip') }}"
                                               required
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('shipping_zip')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-xl font-semibold mb-4">Order Items</h2>
                            <div class="space-y-4">
                                @foreach($cartItems as $item)
                                    <div class="flex items-center gap-4 pb-4 border-b last:border-b-0">
                                        <div class="w-16 h-16 flex-shrink-0">
                                            @if($item['product']->image)
                                                <img src="{{ asset('storage/' . $item['product']->image) }}"
                                                     alt="{{ $item['product']->name }}"
                                                     class="w-full h-full object-cover rounded">
                                            @else
                                                <div class="w-full h-full bg-gray-200 rounded"></div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-900">{{ $item['product']->name }}</h3>
                                            <p class="text-sm text-gray-500">Quantity: {{ $item['quantity'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">${{ number_format($item['subtotal'], 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow p-6 sticky top-8">
                            <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal</span>
                                    <span>${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Tax (16%)</span>
                                    <span>${{ number_format($tax, 2) }}</span>
                                </div>
                                <div class="border-t pt-3 flex justify-between text-xl font-bold">
                                    <span>Total</span>
                                    <span>${{ number_format($total, 2) }}</span>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-lg font-medium mb-3">
                                Place Order
                            </button>

                            <a href="{{ route('cart.index') }}" class="block text-center text-gray-600 hover:text-gray-900">
                                Back to Cart
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

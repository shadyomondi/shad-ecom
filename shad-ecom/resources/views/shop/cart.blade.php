<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8" x-data="{ showAlert: @if(session('success') || session('error')) true @else false @endif }">
        <!-- Alert System -->
        <div x-show="showAlert"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="fixed top-4 right-4 z-50 max-w-sm">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg">
                    <div class="flex items-center justify-between">
                        <p class="font-bold">Success!</p>
                        <button @click="showAlert = false" class="text-green-700 hover:text-green-900">×</button>
                    </div>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg">
                    <div class="flex items-center justify-between">
                        <p class="font-bold">Error!</p>
                        <button @click="showAlert = false" class="text-red-700 hover:text-red-900">×</button>
                    </div>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            @endif
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

            @if(empty($cartItems))
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Your cart is empty</h2>
                    <p class="text-gray-500 mb-6">Add some products to get started!</p>
                    <a href="{{ route('shop.index') }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium">
                        Continue Shopping
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow">
                            @foreach($cartItems as $item)
                                <div class="flex items-center gap-4 p-6 border-b last:border-b-0">
                                    <div class="w-24 h-24 flex-shrink-0">
                                        @if($item['product']->image)
                                            <img src="{{ asset('storage/' . $item['product']->image) }}"
                                                 alt="{{ $item['product']->name }}"
                                                 class="w-full h-full object-cover rounded">
                                        @else
                                            <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                                                <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-lg text-gray-900">{{ $item['product']->name }}</h3>
                                        <p class="text-gray-500 text-sm">{{ $item['product']->category->name }}</p>
                                        <p class="text-lg font-bold text-gray-900 mt-1">${{ number_format($item['product']->price, 2) }}</p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <form action="{{ route('cart.update', $item['product']->id) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number"
                                                   name="quantity"
                                                   value="{{ $item['quantity'] }}"
                                                   min="1"
                                                   max="{{ $item['product']->stock }}"
                                                   class="w-20 rounded border-gray-300 text-center">
                                            <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded text-sm">
                                                Update
                                            </button>
                                        </form>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-gray-900">${{ number_format($item['subtotal'], 2) }}</p>
                                            <form action="{{ route('cart.remove', $item['product']->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                                    Clear Cart
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow p-6 sticky top-8">
                            <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                            <div class="space-y-3 mb-4">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal</span>
                                    <span>${{ number_format($total, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Tax (16%)</span>
                                    <span>${{ number_format($total * 0.16, 2) }}</span>
                                </div>
                                <div class="border-t pt-3 flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span>${{ number_format($total * 1.16, 2) }}</span>
                                </div>
                            </div>
                            @auth
                                <a href="{{ route('checkout.index') }}" class="block w-full bg-blue-500 hover:bg-blue-600 text-white text-center py-3 rounded-lg font-medium mb-3">
                                    Proceed to Checkout
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="block w-full bg-blue-500 hover:bg-blue-600 text-white text-center py-3 rounded-lg font-medium mb-3">
                                    Login to Checkout
                                </a>
                            @endauth
                            <a href="{{ route('shop.index') }}" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 text-center py-3 rounded-lg font-medium">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

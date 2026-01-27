@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if(empty($cart) || count($cart) == 0)
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <h2 class="text-2xl font-semibold text-gray-900 mb-2">Your cart is empty</h2>
                <p class="text-gray-600 mb-6">Add some products to get started!</p>
                <a href="{{ route('shop.index') }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                    Continue Shopping
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        @foreach($cart as $id => $item)
                            <div class="flex items-center gap-4 p-6 border-b last:border-b-0">
                                <div class="w-24 h-24 flex-shrink-0">
                                    @if($item['image'])
                                        <img src="{{ asset('storage/' . $item['image']) }}"
                                             alt="{{ $item['name'] }}"
                                             class="w-full h-full object-cover rounded">
                                    @else
                                        <div class="w-full h-full bg-gray-200 rounded"></div>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $item['name'] }}</h3>
                                    <p class="text-gray-600">KSh {{ number_format($item['price'], 2) }} each</p>

                                    <div class="flex items-center gap-4 mt-3">
                                        <form action="{{ route('cart.update', $id) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <label class="text-sm text-gray-600">Qty:</label>
                                            <input type="number"
                                                   name="quantity"
                                                   value="{{ $item['quantity'] }}"
                                                   min="1"
                                                   class="w-20 px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                            <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Update
                                            </button>
                                        </form>

                                        <form action="{{ route('cart.remove', $id) }}" method="POST" class="ml-auto">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <p class="text-xl font-bold text-gray-900">
                                        KSh {{ number_format($item['price'] * $item['quantity'], 2) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <form action="{{ route('cart.clear') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:text-red-800 font-medium"
                                    onclick="return confirm('Are you sure you want to clear your cart?')">
                                Clear Cart
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal ({{ count($cart) }} items)</span>
                                <span>KSh {{ number_format($total, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span>Calculated at checkout</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between text-xl font-bold text-gray-900">
                                <span>Total</span>
                                <span>KSh {{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        @auth
                            <a href="{{ route('checkout.index') }}"
                               class="block w-full bg-blue-500 hover:bg-blue-700 text-white text-center font-bold py-3 px-6 rounded-lg mb-3 transition">
                                Proceed to Checkout
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="block w-full bg-blue-500 hover:bg-blue-700 text-white text-center font-bold py-3 px-6 rounded-lg mb-3 transition">
                                Login to Checkout
                            </a>
                        @endauth

                        <a href="{{ route('shop.index') }}"
                           class="block w-full text-center text-blue-600 hover:text-blue-800 font-medium py-2">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

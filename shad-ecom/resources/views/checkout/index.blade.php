@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-background-light dark:bg-background-dark py-12">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <h1 class="text-3xl font-bold text-[#111418] dark:text-white mb-8">Checkout</h1>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Shipping Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-[#212b36] rounded-xl border border-[#f0f2f4] dark:border-[#2a3441] p-6 mb-6">
                        <h2 class="text-xl font-bold text-[#111418] dark:text-white mb-6">Shipping Information</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-[#111418] dark:text-white mb-2">
                                    Shipping Address <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="shipping_address" value="{{ old('shipping_address') }}" required
                                       class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a3441] bg-white dark:bg-[#212b36] text-[#111418] dark:text-white focus:ring-2 focus:ring-primary"
                                       placeholder="123 Main Street">
                                @error('shipping_address')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#111418] dark:text-white mb-2">
                                        City <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" required
                                           class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a3441] bg-white dark:bg-[#212b36] text-[#111418] dark:text-white focus:ring-2 focus:ring-primary"
                                           placeholder="Nairobi">
                                    @error('shipping_city')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-[#111418] dark:text-white mb-2">
                                        Postal Code <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="shipping_zip" value="{{ old('shipping_zip') }}" required
                                           class="w-full px-4 py-3 rounded-lg border border-[#dbe0e6] dark:border-[#2a3441] bg-white dark:bg-[#212b36] text-[#111418] dark:text-white focus:ring-2 focus:ring-primary"
                                           placeholder="00100">
                                    @error('shipping_zip')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white dark:bg-[#212b36] rounded-xl border border-[#f0f2f4] dark:border-[#2a3441] p-6">
                        <h2 class="text-xl font-bold text-[#111418] dark:text-white mb-6">Order Items</h2>

                        <div class="space-y-4">
                            @foreach($cart as $id => $item)
                                <div class="flex items-center gap-4 pb-4 border-b border-[#f0f2f4] dark:border-[#2a3441] last:border-b-0">
                                    <div class="w-20 h-20 flex-shrink-0">
                                        @if($item['image'])
                                            <img src="{{ asset('storage/' . $item['image']) }}"
                                                 alt="{{ $item['name'] }}"
                                                 class="w-full h-full object-cover rounded-lg">
                                        @else
                                            <div class="w-full h-full bg-[#f0f2f4] dark:bg-[#2a3441] rounded-lg"></div>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <h3 class="font-semibold text-[#111418] dark:text-white">{{ $item['name'] }}</h3>
                                        <p class="text-sm text-[#617589]">Quantity: {{ $item['quantity'] }}</p>
                                        <p class="text-sm text-[#617589]">KSh {{ number_format($item['price'], 2) }} each</p>
                                    </div>

                                    <div class="text-right">
                                        <p class="font-bold text-lg text-[#111418] dark:text-white">
                                            KSh {{ number_format($item['price'] * $item['quantity'], 2) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-[#212b36] rounded-xl border border-[#f0f2f4] dark:border-[#2a3441] p-6 sticky top-4">
                        <h2 class="text-xl font-bold text-[#111418] dark:text-white mb-6">Order Summary</h2>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-[#617589]">
                                <span>Subtotal</span>
                                <span>KSh {{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-[#617589]">
                                <span>Tax (8%)</span>
                                <span>KSh {{ number_format($tax, 2) }}</span>
                            </div>
                            <div class="border-t border-[#f0f2f4] dark:border-[#2a3441] pt-3 flex justify-between text-xl font-bold text-[#111418] dark:text-white">
                                <span>Total</span>
                                <span>KSh {{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <div class="bg-[#f0f2f4] dark:bg-[#2a3441] rounded-lg p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-primary text-xl mt-0.5">info</span>
                                <div class="text-sm text-[#617589]">
                                    <p class="font-semibold text-[#111418] dark:text-white mb-1">Payment via M-Pesa</p>
                                    <p>You will receive M-Pesa payment instructions after placing your order.</p>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full bg-primary hover:bg-blue-600 text-white font-bold py-4 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">credit_card</span>
                            <span>Place Order</span>
                        </button>

                        <a href="{{ route('cart.index') }}"
                           class="block w-full text-center text-[#617589] hover:text-primary font-medium py-3 mt-3">
                            ← Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

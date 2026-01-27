@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-background-light dark:bg-background-dark py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
            <a href="{{ route('shop.index') }}" class="hover:text-primary">Shop</a>
            @if($product->category)
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <a href="{{ route('shop.index', ['category' => $product->category_id]) }}" class="hover:text-primary">
                {{ $product->category->name }}
            </a>
            @endif
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-gray-900 dark:text-white">{{ $product->name }}</span>
        </nav>

        <!-- Product Details -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 mb-12">
            <!-- Product Image -->
            <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-auto object-cover">
                @else
                    <div class="w-full aspect-square flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                        <span class="material-symbols-outlined text-gray-400 text-9xl">
                            image
                        </span>
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <!-- Product Name -->
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ $product->name }}
                </h1>

                <!-- Price -->
                <div class="mb-6">
                    <span class="text-4xl font-bold text-primary">
                        KSh {{ number_format($product->price, 2) }}
                    </span>
                </div>

                <!-- Stock Status -->
                <div class="mb-6">
                    @if($product->stock > 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            <span class="material-symbols-outlined text-base mr-1">check_circle</span>
                            In Stock ({{ $product->stock }} available)
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                            <span class="material-symbols-outlined text-base mr-1">cancel</span>
                            Out of Stock
                        </span>
                    @endif
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Description</h2>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ $product->description }}
                    </p>
                </div>

                <!-- Product Details -->
                <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Product Details</h3>
                    <div class="space-y-2 text-sm">
                        @if($product->category)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Category:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $product->category->name }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">SKU:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $product->sku }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Availability:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $product->stock > 0 ? $product->stock . ' units' : 'Out of stock' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Add to Cart Form -->
                @if($product->stock > 0)
                <form action="{{ route('cart.add', $product) }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Quantity
                        </label>
                        <div class="flex items-center space-x-4">
                            <input type="number"
                                   name="quantity"
                                   id="quantity"
                                   min="1"
                                   max="{{ $product->stock }}"
                                   value="1"
                                   class="w-24 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                Max: {{ $product->stock }}
                            </span>
                        </div>
                    </div>

                    <!-- Add to Cart Button -->
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-8 py-4 bg-primary text-white text-lg font-semibold rounded-lg hover:bg-primary/90 transition-colors shadow-lg">
                        <span class="material-symbols-outlined text-2xl mr-2">
                            shopping_cart
                        </span>
                        Add to Cart
                    </button>
                </form>
                @else
                <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <p class="text-red-800 dark:text-red-400 text-sm">
                        This product is currently out of stock. Please check back later.
                    </p>
                </div>
                @endif

                <!-- Additional Info -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-6 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-base mr-1">local_shipping</span>
                            <span>Free Shipping</span>
                        </div>
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-base mr-1">cached</span>
                            <span>Easy Returns</span>
                        </div>
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-base mr-1">verified_user</span>
                            <span>Secure Payment</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Related Products</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                <a href="{{ route('shop.show', $relatedProduct) }}"
                   class="group bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700">
                    <!-- Product Image -->
                    <div class="relative aspect-square overflow-hidden bg-gray-100 dark:bg-gray-700">
                        @if($relatedProduct->image)
                            <img src="{{ asset('storage/' . $relatedProduct->image) }}"
                                 alt="{{ $relatedProduct->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-gray-400 text-5xl">
                                    image
                                </span>
                            </div>
                        @endif

                        <!-- Stock Badge -->
                        @if($relatedProduct->stock <= 0)
                        <div class="absolute top-2 right-2">
                            <span class="px-2 py-1 bg-red-500 text-white text-xs font-semibold rounded">
                                Out of Stock
                            </span>
                        </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1 line-clamp-2 group-hover:text-primary transition-colors">
                            {{ $relatedProduct->name }}
                        </h3>
                        @if($relatedProduct->category)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                            {{ $relatedProduct->category->name }}
                        </p>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-primary">
                                KSh {{ number_format($relatedProduct->price, 2) }}
                            </span>
                            @if($relatedProduct->stock > 0)
                            <span class="text-xs text-green-600 dark:text-green-400">
                                {{ $relatedProduct->stock }} in stock
                            </span>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

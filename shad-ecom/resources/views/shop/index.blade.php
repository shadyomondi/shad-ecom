@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-background-light dark:bg-background-dark">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-r from-primary to-blue-600 text-white py-12">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
            <h1 class="text-4xl font-bold mb-2">Welcome to ShopModern</h1>
            <p class="text-lg opacity-90">Discover amazing products at unbeatable prices</p>
        </div>
    </div>

    <div class="max-w-[1440px] mx-auto px-6 lg:px-10 py-8">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 mb-6">
            <a class="text-[#617589] text-sm font-medium hover:text-primary" href="{{ route('shop.index') }}">Home</a>
            <span class="text-[#617589] text-sm">/</span>
            <span class="text-[#111418] dark:text-white text-sm font-medium">
                @if(request('category'))
                    {{ $categories->where('id', request('category'))->first()->name ?? 'Products' }}
                @else
                    All Products
                @endif
            </span>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-64 flex flex-col gap-8 shrink-0">
                <div>
                    <h3 class="text-[#111418] dark:text-white font-semibold mb-4 text-base">Categories</h3>
                    <div class="flex flex-col gap-1">
                        <a href="{{ route('shop.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ !request('category') ? 'bg-primary/10 text-primary' : 'hover:bg-[#f0f2f4] dark:hover:bg-[#212b36]' }} group">
                            <span class="material-symbols-outlined text-xl {{ !request('category') ? 'text-primary' : 'text-[#617589] group-hover:text-primary' }}">grid_view</span>
                            <p class="text-sm font-{{ !request('category') ? 'semibold' : 'medium' }}">All Products</p>
                        </a>
                        @foreach($categories as $category)
                        <a href="{{ route('shop.index', ['category' => $category->id]) }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request('category') == $category->id ? 'bg-primary/10 text-primary' : 'hover:bg-[#f0f2f4] dark:hover:bg-[#212b36]' }} group">
                            <span class="material-symbols-outlined text-xl {{ request('category') == $category->id ? 'text-primary' : 'text-[#617589] group-hover:text-primary' }}">{{ $category->icon ?? 'category' }}</span>
                            <p class="text-[#111418] dark:text-white text-sm font-medium">{{ $category->name }}</p>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Price Filter -->
                <div class="border-t border-[#f0f2f4] dark:border-[#2a3441] pt-6">
                    <h3 class="text-[#111418] dark:text-white font-semibold mb-6 text-base">Price Range</h3>
                    <form action="{{ route('shop.index') }}" method="GET" class="space-y-4">
                        @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <div>
                            <label class="text-sm text-[#617589] font-medium">Min Price</label>
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="$0"
                                   class="w-full mt-1 px-3 py-2 rounded-lg border border-[#dbe0e6] dark:border-[#2a3441] bg-white dark:bg-[#212b36] focus:ring-2 focus:ring-primary text-sm">
                        </div>
                        <div>
                            <label class="text-sm text-[#617589] font-medium">Max Price</label>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="$1000+"
                                   class="w-full mt-1 px-3 py-2 rounded-lg border border-[#dbe0e6] dark:border-[#2a3441] bg-white dark:bg-[#212b36] focus:ring-2 focus:ring-primary text-sm">
                        </div>
                        <button type="submit" class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors text-sm font-semibold">
                            Apply Filter
                        </button>
                    </form>
                </div>

                <!-- Availability Filter -->
                <div class="border-t border-[#f0f2f4] dark:border-[#2a3441] pt-6">
                    <h3 class="text-[#111418] dark:text-white font-semibold mb-4 text-base">Availability</h3>
                    <div class="flex flex-col gap-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" class="rounded text-primary focus:ring-primary size-4" checked>
                            <span class="text-sm font-medium">In Stock</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" class="rounded text-primary focus:ring-primary size-4">
                            <span class="text-sm font-medium">On Sale</span>
                        </label>
                    </div>
                </div>

                @if(request()->hasAny(['category', 'min_price', 'max_price', 'search']))
                <a href="{{ route('shop.index') }}" class="w-full mt-4 py-2 border border-[#dbe0e6] dark:border-[#2a3441] rounded-lg text-sm font-semibold hover:bg-[#f0f2f4] dark:hover:bg-[#212b36] transition-colors text-center block">
                    Clear Filters
                </a>
                @endif
            </aside>

            <!-- Main Content -->
            <main class="flex-1">
                <!-- Header with Search and Sort -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        <h1 class="text-2xl font-bold">
                            @if(request('category'))
                                {{ $categories->where('id', request('category'))->first()->name ?? 'Products' }}
                            @else
                                All Products
                            @endif
                        </h1>
                        <p class="text-[#617589] text-sm">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex bg-white dark:bg-[#212b36] border border-[#f0f2f4] dark:border-[#2a3441] rounded-lg p-1">
                            <button class="flex items-center justify-center size-8 rounded-md bg-[#f0f2f4] dark:bg-[#2a3441] text-primary">
                                <span class="material-symbols-outlined text-sm">grid_view</span>
                            </button>
                            <button class="flex items-center justify-center size-8 rounded-md hover:bg-[#f0f2f4] dark:hover:bg-[#2a3441] text-[#617589]">
                                <span class="material-symbols-outlined text-sm">view_list</span>
                            </button>
                        </div>
                        <select onchange="window.location.href=this.value"
                                class="bg-white dark:bg-[#212b36] border-[#f0f2f4] dark:border-[#2a3441] rounded-lg text-sm font-medium px-4 h-10 focus:ring-primary">
                            <option value="{{ route('shop.index', array_merge(request()->except('sort'), ['sort' => 'latest'])) }}"
                                    {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Sort by: Newest</option>
                            <option value="{{ route('shop.index', array_merge(request()->except('sort'), ['sort' => 'popularity'])) }}"
                                    {{ request('sort') == 'popularity' ? 'selected' : '' }}>Sort by: Popularity</option>
                            <option value="{{ route('shop.index', array_merge(request()->except('sort'), ['sort' => 'price_low'])) }}"
                                    {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="{{ route('shop.index', array_merge(request()->except('sort'), ['sort' => 'price_high'])) }}"
                                    {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                    <div class="group bg-white dark:bg-[#212b36] rounded-xl border border-[#f0f2f4] dark:border-[#2a3441] overflow-hidden hover:shadow-xl transition-all duration-300">
                        <a href="{{ route('shop.show', $product->slug) }}" class="relative block aspect-square bg-[#f8f9fa] overflow-hidden">
                            @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-[#f0f2f4]">
                                <span class="material-symbols-outlined text-6xl text-[#617589]">image</span>
                            </div>
                            @endif

                            <!-- Badges -->
                            @if($product->is_featured || $product->original_price)
                            <div class="absolute top-3 left-3">
                                @if($product->is_featured)
                                <span class="bg-primary text-white text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded">New</span>
                                @elseif($product->original_price)
                                <span class="bg-orange-500 text-white text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded">
                                    {{ round((($product->original_price - $product->price) / $product->original_price) * 100) }}% Off
                                </span>
                                @endif
                            </div>
                            @endif

                            <!-- Favorite Button -->
                            <button class="absolute top-3 right-3 size-8 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="material-symbols-outlined text-lg text-red-500">favorite</span>
                            </button>
                        </a>

                        <div class="p-4">
                            <!-- Stock Status -->
                            <div class="flex items-center gap-1 mb-2">
                                @if($product->stock > 10)
                                <span class="size-2 rounded-full bg-emerald-500"></span>
                                <span class="text-emerald-500 text-[11px] font-bold uppercase tracking-tight">In Stock</span>
                                @elseif($product->stock > 0)
                                <span class="size-2 rounded-full bg-orange-500 animate-pulse"></span>
                                <span class="text-orange-500 text-[11px] font-bold uppercase tracking-tight">Only {{ $product->stock }} Left!</span>
                                @else
                                <span class="size-2 rounded-full bg-red-500"></span>
                                <span class="text-red-500 text-[11px] font-bold uppercase tracking-tight">Out of Stock</span>
                                @endif
                            </div>

                            <a href="{{ route('shop.show', $product->slug) }}">
                                <h3 class="text-[#111418] dark:text-white font-medium text-sm line-clamp-1 group-hover:text-primary transition-colors">
                                    {{ $product->name }}
                                </h3>
                            </a>

                            <div class="mt-2 flex items-center justify-between">
                                <div>
                                    <p class="text-lg font-bold text-[#111418] dark:text-white">KSh {{ number_format($product->price, 2) }}</p>
                                    @if($product->original_price)
                                    <p class="text-xs text-[#617589] line-through">KSh {{ number_format($product->original_price, 2) }}</p>
                                    @endif
                                </div>

                                @if($product->stock > 0)
                                <form action="{{ route('cart.add', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="size-9 bg-[#f0f2f4] dark:bg-[#2a3441] rounded-lg flex items-center justify-center hover:bg-primary hover:text-white transition-all">
                                        <span class="material-symbols-outlined text-xl">add_shopping_cart</span>
                                    </button>
                                </form>
                                @else
                                <button disabled class="size-9 bg-[#f0f2f4] dark:bg-[#2a3441] rounded-lg flex items-center justify-center opacity-50 cursor-not-allowed">
                                    <span class="material-symbols-outlined text-xl">block</span>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12 flex items-center justify-center gap-2">
                    {{ $products->links() }}
                </div>
                @else
                <!-- Empty State -->
                <div class="bg-white dark:bg-[#212b36] rounded-xl border border-[#f0f2f4] dark:border-[#2a3441] p-12 text-center">
                    <span class="material-symbols-outlined text-6xl text-[#617589] mb-4 block">search_off</span>
                    <h3 class="text-xl font-semibold text-[#111418] dark:text-white mb-2">No Products Found</h3>
                    <p class="text-[#617589] mb-6">We couldn't find any products matching your criteria.</p>
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors font-medium">
                        <span class="material-symbols-outlined">refresh</span>
                        <span>Clear Filters</span>
                    </a>
                </div>
                @endif
            </main>
        </div>
    </div>
</div>
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Debug Info -->
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                <strong>Debug Info:</strong>
                Products: {{ $totalProducts }} |
                Low Stock: {{ $lowStockProducts->count() }} |
                Revenue: KSh {{ number_format($totalRevenue, 2) }} |
                Pending: {{ $pendingPayments }}
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 mb-1">Total Revenue</div>
                    <div class="text-2xl font-bold text-gray-900">KSh {{ number_format($totalRevenue, 2) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 mb-1">Pending Payments</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $pendingPayments }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 mb-1">Total Products</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $totalProducts }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 mb-1">Low Stock Items</div>
                    <div class="text-2xl font-bold text-red-600">{{ $lowStockProducts->count() }}</div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            @if($lowStockProducts->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Low Stock Alert</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($lowStockProducts as $product)
                                        <tr class="{{ $product->stock == 0 ? 'bg-red-50' : ($product->stock <= 5 ? 'bg-yellow-50' : '') }}">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    @if($product->image)
                                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-10 w-10 object-cover rounded mr-3">
                                                    @else
                                                        <div class="h-10 w-10 bg-gray-200 rounded mr-3"></div>
                                                    @endif
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                        <div class="text-xs text-gray-500">{{ $product->sku }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $product->category ? $product->category->name : 'N/A' }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $product->stock == 0 ? 'bg-red-100 text-red-800' :
                                                       ($product->stock <= 5 ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ $product->stock }} {{ $product->stock == 0 ? '(Out of Stock)' : 'units' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">KSh {{ number_format($product->price, 2) }}</td>
                                            <td class="px-6 py-4 text-sm">
                                                <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                    Restock
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Pending Payments (Mock Payment Trigger) -->
            @if($pendingOrders->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pending Payments (Testing)</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pendingOrders as $order)
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ $order->order_number }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $order->user->name }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                KSh {{ number_format($order->total, 2) }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $order->created_at->format('M d, Y h:i A') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <form action="{{ route('admin.confirm-payment', $order) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-xs"
                                                            onclick="return confirm('Confirm payment for {{ $order->order_number }}?')">
                                                        Mock Payment Confirm
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Links -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('admin.products.index') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <div class="text-lg font-semibold text-gray-900">Manage Products</div>
                            <div class="text-sm text-gray-500">View and edit all products</div>
                        </a>
                        <a href="{{ route('admin.products.create') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <div class="text-lg font-semibold text-gray-900">Add New Product</div>
                            <div class="text-sm text-gray-500">Create a new product listing</div>
                        </a>
                        <a href="{{ route('shop.index') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <div class="text-lg font-semibold text-gray-900">View Shop</div>
                            <div class="text-sm text-gray-500">See the customer view</div>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

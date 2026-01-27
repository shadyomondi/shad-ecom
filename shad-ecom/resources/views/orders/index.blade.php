<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Orders') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @forelse($orders as $order)
                        <div class="border rounded-lg p-6 mb-4 hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Order {{ $order->order_number }}</h3>
                                    <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-bold text-gray-900">KSh {{ number_format($order->total, 2) }}</p>
                                    <div class="flex gap-2 mt-2">
                                        <span class="px-2 py-1 text-xs font-semibold rounded
                                            {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex gap-4 overflow-x-auto pb-2">
                                    @foreach($order->items->take(4) as $item)
                                        <div class="flex-shrink-0">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}"
                                                     alt="{{ $item->product->name }}"
                                                     class="h-16 w-16 object-cover rounded">
                                            @else
                                                <div class="h-16 w-16 bg-gray-200 rounded"></div>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($order->items->count() > 4)
                                        <div class="flex-shrink-0 h-16 w-16 bg-gray-100 rounded flex items-center justify-center">
                                            <span class="text-sm text-gray-600">+{{ $order->items->count() - 4 }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex gap-3 mt-4">
                                <a href="{{ route('orders.show', $order) }}"
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded font-medium text-sm">
                                    View Details
                                </a>
                                @if($order->payment_status === 'paid')
                                    <a href="{{ route('orders.invoice', $order) }}"
                                       class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded font-medium text-sm">
                                        Download Invoice
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No orders yet</h3>
                            <p class="text-gray-500 mb-6">Start shopping to see your orders here!</p>
                            <a href="{{ route('shop.index') }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium">
                                Start Shopping
                            </a>
                        </div>
                    @endforelse

                    @if($orders->hasPages())
                        <div class="mt-6">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

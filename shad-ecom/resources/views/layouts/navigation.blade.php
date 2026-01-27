<nav x-data="{ open: false }" class="sticky top-0 z-50 w-full border-b border-[#f0f2f4] dark:border-[#2a3441] bg-white/80 dark:bg-background-dark/80 backdrop-blur-md">
    <!-- Primary Navigation Menu -->
    <div class="max-w-[1440px] mx-auto px-6 lg:px-10 py-3 flex items-center justify-between">
        <div class="flex items-center gap-8 flex-1">
            <!-- Logo -->
            <a href="{{ route('shop.index') }}" class="flex items-center gap-2 text-[#111418] dark:text-white">
                <div class="size-8 bg-primary rounded-lg flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-lg">grid_view</span>
                </div>
                <h2 class="text-xl font-bold tracking-tight">ShopModern</h2>
            </a>

            <!-- Search Bar (Desktop) -->
            <div class="hidden md:flex flex-1 max-w-md">
                <form action="{{ route('shop.search') }}" method="GET" class="w-full">
                    <label class="relative w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#617589]">
                            <span class="material-symbols-outlined text-xl">search</span>
                        </span>
                        <input name="q" value="{{ request('q') }}"
                               class="w-full h-10 pl-10 pr-4 bg-[#f0f2f4] dark:bg-[#212b36] border-none rounded-lg focus:ring-2 focus:ring-primary/50 text-sm"
                               placeholder="Search products, brands, and more..."/>
                    </label>
                </form>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <!-- Navigation Links (Desktop) -->
            <nav class="hidden lg:flex items-center gap-6">
                <a class="text-sm font-medium hover:text-primary transition-colors" href="{{ route('shop.index') }}">Shop</a>
                @auth
                <a class="text-sm font-medium hover:text-primary transition-colors" href="{{ route('orders.index') }}">My Orders</a>
                @if(Auth::user()->is_admin)
                <a class="text-sm font-medium hover:text-primary transition-colors" href="{{ route('admin.dashboard') }}">Admin</a>
                @endif
                @endauth
            </nav>

            <div class="h-6 w-px bg-[#f0f2f4] dark:bg-[#2a3441] mx-2"></div>

            <div class="flex items-center gap-2">
                <!-- Cart Button -->
                <a href="{{ route('cart.index') }}" class="relative flex items-center justify-center size-10 rounded-lg bg-[#f0f2f4] dark:bg-[#212b36] hover:bg-primary/10 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    @if(session('cart') && count(session('cart')) > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        {{ count(session('cart')) }}
                    </span>
                    @endif
                </a>

                @auth
                <!-- User Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center justify-center size-10 rounded-lg bg-[#f0f2f4] dark:bg-[#212b36] hover:bg-primary/10 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined">person</span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('orders.index')">
                            {{ __('My Orders') }}
                        </x-dropdown-link>

                        @if(Auth::user()->is_admin)
                        <x-dropdown-link :href="route('admin.dashboard')">
                            {{ __('Admin Dashboard') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('admin.products.index')">
                            {{ __('Manage Products') }}
                        </x-dropdown-link>
                        @endif

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                <!-- Login & Register Links for Guests -->
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-primary transition-colors">
                    Log In
                </a>
                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">
                    Sign Up
                </a>
                @endauth
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        @auth
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4 space-y-1">
                <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">Login</a>
                <a href="{{ route('register') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">Register</a>
            </div>
        </div>
        @endauth
    </div>
</nav>

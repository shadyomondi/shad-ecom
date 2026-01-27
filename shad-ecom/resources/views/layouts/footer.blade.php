<!-- Footer -->
<footer class="mt-auto border-t border-[#f0f2f4] dark:border-[#2a3441] bg-white dark:bg-background-dark py-12 px-6 lg:px-10">
    <div class="max-w-[1440px] mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
        <div class="col-span-1 md:col-span-1">
            <div class="flex items-center gap-2 text-[#111418] dark:text-white mb-4">
                <div class="size-6 bg-primary rounded flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-sm">grid_view</span>
                </div>
                <h2 class="text-lg font-bold tracking-tight">ShopModern</h2>
            </div>
            <p class="text-sm text-[#617589] leading-relaxed">The ultimate destination for modern shopping. Quality products, secure checkout, and lightning-fast delivery.</p>
        </div>

        <div>
            <h4 class="font-bold text-sm mb-4 uppercase tracking-wider">Shopping</h4>
            <ul class="flex flex-col gap-3 text-sm text-[#617589]">
                <li><a class="hover:text-primary" href="{{ route('shop.index') }}">All Products</a></li>
                <li><a class="hover:text-primary" href="{{ route('shop.index', ['sort' => 'newest']) }}">New Arrivals</a></li>
                <li><a class="hover:text-primary" href="{{ route('shop.index') }}">Categories</a></li>
            </ul>
        </div>

        <div>
            <h4 class="font-bold text-sm mb-4 uppercase tracking-wider">Support</h4>
            <ul class="flex flex-col gap-3 text-sm text-[#617589]">
                <li><a class="hover:text-primary" href="#">Contact Us</a></li>
                <li><a class="hover:text-primary" href="#">Shipping Info</a></li>
                <li><a class="hover:text-primary" href="#">Returns & Exchanges</a></li>
                <li><a class="hover:text-primary" href="#">FAQs</a></li>
            </ul>
        </div>

        <div>
            <h4 class="font-bold text-sm mb-4 uppercase tracking-wider">Newsletter</h4>
            <p class="text-sm text-[#617589] mb-4">Subscribe for early access and exclusive deals.</p>
            <div class="flex gap-2">
                <input class="flex-1 h-10 px-4 bg-[#f0f2f4] dark:bg-[#212b36] border-none rounded-lg text-sm" placeholder="Email address"/>
                <button class="px-4 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition-colors">Join</button>
            </div>
        </div>
    </div>

    <div class="max-w-[1440px] mx-auto mt-12 pt-8 border-t border-[#f0f2f4] dark:border-[#2a3441] flex flex-col md:flex-row justify-between items-center gap-4">
        <p class="text-xs text-[#617589]">© {{ date('Y') }} ShopModern Inc. All rights reserved.</p>
        <div class="flex gap-6 text-[#617589]">
            <a class="hover:text-primary" href="#"><span class="material-symbols-outlined text-lg">public</span></a>
            <a class="hover:text-primary" href="#"><span class="material-symbols-outlined text-lg">chat</span></a>
            <a class="hover:text-primary" href="#"><span class="material-symbols-outlined text-lg">share</span></a>
        </div>
    </div>
</footer>
</footer>

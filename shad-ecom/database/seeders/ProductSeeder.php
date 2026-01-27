<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories
        $electronics = Category::firstOrCreate(
            ['slug' => 'electronics'],
            ['name' => 'Electronics', 'icon' => 'devices']
        );

        $fashion = Category::firstOrCreate(
            ['slug' => 'fashion'],
            ['name' => 'Fashion', 'icon' => 'apparel']
        );

        // Electronics products
        $electronicsProducts = [
            ['name' => 'Wireless Bluetooth Headphones', 'price' => 79.99, 'original_price' => 99.99, 'stock' => 50, 'description' => 'Premium noise-cancelling wireless headphones with 30-hour battery life.'],
            ['name' => 'Smart Watch Pro', 'price' => 249.99, 'original_price' => 299.99, 'stock' => 30, 'description' => 'Advanced fitness tracking with heart rate monitor and GPS.'],
            ['name' => '4K Webcam', 'price' => 89.99, 'stock' => 45, 'description' => 'Crystal clear 4K video quality for streaming and video calls.'],
            ['name' => 'USB-C Hub 7-in-1', 'price' => 39.99, 'original_price' => 59.99, 'stock' => 100, 'description' => 'Expand your laptop connectivity with multiple ports.'],
            ['name' => 'Wireless Gaming Mouse', 'price' => 59.99, 'stock' => 75, 'description' => 'High precision gaming mouse with customizable RGB lighting.'],
            ['name' => 'Mechanical Keyboard RGB', 'price' => 129.99, 'original_price' => 159.99, 'stock' => 40, 'description' => 'Tactile mechanical switches with customizable RGB backlighting.'],
            ['name' => 'Portable SSD 1TB', 'price' => 119.99, 'stock' => 60, 'description' => 'Fast and reliable portable storage with USB 3.2 Gen 2.'],
            ['name' => 'Wireless Charging Pad', 'price' => 29.99, 'stock' => 120, 'description' => 'Fast wireless charging for all Qi-enabled devices.'],
            ['name' => '27" Gaming Monitor 144Hz', 'price' => 349.99, 'original_price' => 449.99, 'stock' => 25, 'description' => 'Immersive gaming experience with high refresh rate.'],
            ['name' => 'Laptop Stand Aluminum', 'price' => 49.99, 'stock' => 80, 'description' => 'Ergonomic laptop stand for better posture.'],
            ['name' => 'Blue Yeti Microphone', 'price' => 129.99, 'stock' => 35, 'description' => 'Professional USB microphone for podcasting and streaming.'],
            ['name' => 'Ring Light 10 inch', 'price' => 39.99, 'stock' => 90, 'description' => 'Perfect lighting for video calls and content creation.'],
            ['name' => 'Graphics Tablet', 'price' => 89.99, 'original_price' => 119.99, 'stock' => 40, 'description' => 'Digital drawing tablet with pressure sensitivity.'],
            ['name' => 'USB Condenser Microphone', 'price' => 69.99, 'stock' => 55, 'description' => 'Studio quality recording for vocals and instruments.'],
            ['name' => 'Laptop Cooling Pad', 'price' => 34.99, 'stock' => 70, 'description' => 'Keep your laptop cool during intensive tasks.'],
            ['name' => 'Bluetooth Speaker Waterproof', 'price' => 49.99, 'stock' => 85, 'description' => 'Portable speaker with 12-hour battery life.'],
            ['name' => 'Smart LED Light Bulbs (4-Pack)', 'price' => 44.99, 'stock' => 95, 'description' => 'WiFi-enabled color-changing smart bulbs.'],
            ['name' => 'VR Headset', 'price' => 399.99, 'original_price' => 499.99, 'stock' => 20, 'description' => 'Immersive virtual reality gaming experience.'],
            ['name' => 'Action Camera 4K', 'price' => 199.99, 'stock' => 30, 'description' => 'Waterproof action camera for adventure recording.'],
            ['name' => 'Power Bank 20000mAh', 'price' => 39.99, 'stock' => 110, 'description' => 'High-capacity portable charger for all devices.'],
            ['name' => 'Wireless Earbuds Pro', 'price' => 149.99, 'original_price' => 199.99, 'stock' => 65, 'description' => 'True wireless earbuds with active noise cancellation.'],
            ['name' => 'Smart Plug (2-Pack)', 'price' => 24.99, 'stock' => 130, 'description' => 'Control your devices remotely with voice commands.'],
            ['name' => 'External DVD Drive', 'price' => 29.99, 'stock' => 45, 'description' => 'USB portable DVD/CD reader and writer.'],
            ['name' => 'HDMI Cable 6ft', 'price' => 12.99, 'stock' => 200, 'description' => 'High-speed HDMI 2.1 cable for 4K and 8K displays.'],
            ['name' => 'Cable Management Box', 'price' => 19.99, 'stock' => 75, 'description' => 'Organize and hide messy cables.'],
        ];

        foreach ($electronicsProducts as $product) {
            Product::create(array_merge($product, [
                'category_id' => $electronics->id,
                'is_featured' => rand(0, 1) === 1,
                'slug' => \Illuminate\Support\Str::slug($product['name']),
                'sku' => 'PRD-' . strtoupper(\Illuminate\Support\Str::random(8)),
            ]));
        }

        // Fashion products
        $fashionProducts = [
            ['name' => 'Classic Cotton T-Shirt', 'price' => 19.99, 'stock' => 150, 'description' => 'Comfortable 100% cotton t-shirt in various colors.'],
            ['name' => 'Slim Fit Jeans', 'price' => 59.99, 'original_price' => 79.99, 'stock' => 80, 'description' => 'Stylish denim jeans with stretch fabric.'],
            ['name' => 'Leather Jacket', 'price' => 199.99, 'original_price' => 279.99, 'stock' => 25, 'description' => 'Premium genuine leather jacket.'],
            ['name' => 'Running Shoes', 'price' => 89.99, 'stock' => 95, 'description' => 'Lightweight and breathable athletic shoes.'],
            ['name' => 'Hooded Sweatshirt', 'price' => 39.99, 'stock' => 120, 'description' => 'Cozy hoodie perfect for casual wear.'],
            ['name' => 'Polo Shirt', 'price' => 29.99, 'stock' => 100, 'description' => 'Classic polo shirt for smart casual look.'],
            ['name' => 'Winter Coat', 'price' => 149.99, 'original_price' => 199.99, 'stock' => 40, 'description' => 'Warm insulated coat for cold weather.'],
            ['name' => 'Sneakers White', 'price' => 69.99, 'stock' => 110, 'description' => 'Clean white sneakers for everyday wear.'],
            ['name' => 'Baseball Cap', 'price' => 24.99, 'stock' => 85, 'description' => 'Adjustable baseball cap with embroidered logo.'],
            ['name' => 'Leather Belt', 'price' => 34.99, 'stock' => 90, 'description' => 'Genuine leather belt with classic buckle.'],
            ['name' => 'Sunglasses Polarized', 'price' => 49.99, 'original_price' => 69.99, 'stock' => 75, 'description' => 'UV protection polarized sunglasses.'],
            ['name' => 'Backpack', 'price' => 59.99, 'stock' => 70, 'description' => 'Spacious backpack with laptop compartment.'],
            ['name' => 'Dress Shirt', 'price' => 44.99, 'stock' => 65, 'description' => 'Formal dress shirt for office wear.'],
            ['name' => 'Yoga Pants', 'price' => 39.99, 'stock' => 130, 'description' => 'Stretchy and comfortable workout pants.'],
            ['name' => 'Sports Bra', 'price' => 29.99, 'stock' => 115, 'description' => 'High-support sports bra for active lifestyle.'],
            ['name' => 'Wool Scarf', 'price' => 34.99, 'stock' => 60, 'description' => 'Soft wool scarf for winter warmth.'],
            ['name' => 'Leather Wallet', 'price' => 39.99, 'stock' => 95, 'description' => 'Bifold wallet with card slots.'],
            ['name' => 'Wrist Watch Classic', 'price' => 79.99, 'original_price' => 99.99, 'stock' => 50, 'description' => 'Elegant analog watch with leather strap.'],
            ['name' => 'Crossbody Bag', 'price' => 54.99, 'stock' => 55, 'description' => 'Stylish crossbody bag for everyday use.'],
            ['name' => 'Ankle Boots', 'price' => 89.99, 'stock' => 45, 'description' => 'Fashionable ankle boots with side zipper.'],
            ['name' => 'Denim Jacket', 'price' => 69.99, 'original_price' => 89.99, 'stock' => 60, 'description' => 'Classic denim jacket for layering.'],
            ['name' => 'Socks Pack (6 pairs)', 'price' => 19.99, 'stock' => 140, 'description' => 'Comfortable cotton socks in assorted colors.'],
            ['name' => 'Gloves Winter', 'price' => 24.99, 'stock' => 70, 'description' => 'Warm insulated gloves for cold weather.'],
            ['name' => 'Beanie Hat', 'price' => 14.99, 'stock' => 100, 'description' => 'Knit beanie for winter warmth.'],
            ['name' => 'Tie Silk', 'price' => 29.99, 'stock' => 80, 'description' => 'Elegant silk tie for formal occasions.'],
        ];

        foreach ($fashionProducts as $product) {
            Product::create(array_merge($product, [
                'category_id' => $fashion->id,
                'is_featured' => rand(0, 1) === 1,
                'slug' => \Illuminate\Support\Str::slug($product['name']),
                'sku' => 'PRD-' . strtoupper(\Illuminate\Support\Str::random(8)),
            ]));
        }
    }
}

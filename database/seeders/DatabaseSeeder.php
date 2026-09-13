<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'phone' => '01000000000',
                'address' => 'Cairo, Egypt',
                'role' => 'user',
                'password' => Hash::make('password'),
            ]
        );

        $products = [
            ['name' => 'Cartoon Astronaut T-Shirt', 'brand' => 'adidas', 'price' => 78, 'stock' => 18, 'image_path' => 'img/products/f1.jpg'],
            ['name' => 'Floral Summer Shirt', 'brand' => 'adidas', 'price' => 82, 'stock' => 14, 'image_path' => 'img/products/f2.jpg'],
            ['name' => 'Rose Print Shirt', 'brand' => 'adidas', 'price' => 76, 'stock' => 20, 'image_path' => 'img/products/f3.jpg'],
            ['name' => 'Pastel Leaf Shirt', 'brand' => 'adidas', 'price' => 88, 'stock' => 10, 'image_path' => 'img/products/f4.jpg'],
            ['name' => 'Purple Flower Tee', 'brand' => 'adidas', 'price' => 70, 'stock' => 24, 'image_path' => 'img/products/f5.jpg'],
            ['name' => 'Vintage Pocket Shirt', 'brand' => 'adidas', 'price' => 74, 'stock' => 12, 'image_path' => 'img/products/f6.jpg'],
            ['name' => 'Relaxed Fit Pants', 'brand' => 'adidas', 'price' => 92, 'stock' => 9, 'image_path' => 'img/products/f7.jpg'],
            ['name' => 'Black Sleeve Blouse', 'brand' => 'adidas', 'price' => 84, 'stock' => 16, 'image_path' => 'img/products/f8.jpg'],
            ['name' => 'Blue Oxford Shirt', 'brand' => 'Haga Helwa', 'price' => 66, 'stock' => 18, 'image_path' => 'img/products/n1.jpg'],
            ['name' => 'Grey Check Shirt', 'brand' => 'Haga Helwa', 'price' => 68, 'stock' => 15, 'image_path' => 'img/products/n2.jpg'],
            ['name' => 'White Casual Shirt', 'brand' => 'Haga Helwa', 'price' => 62, 'stock' => 22, 'image_path' => 'img/products/n3.jpg'],
            ['name' => 'Printed Beach Shirt', 'brand' => 'Haga Helwa', 'price' => 73, 'stock' => 11, 'image_path' => 'img/products/n4.jpg'],
            ['name' => 'Denim Button Shirt', 'brand' => 'Haga Helwa', 'price' => 79, 'stock' => 13, 'image_path' => 'img/products/n5.jpg'],
            ['name' => 'Cargo Shorts', 'brand' => 'Haga Helwa', 'price' => 58, 'stock' => 19, 'image_path' => 'img/products/n6.jpg'],
            ['name' => 'Camel Linen Shirt', 'brand' => 'Haga Helwa', 'price' => 81, 'stock' => 8, 'image_path' => 'img/products/n7.jpg'],
            ['name' => 'Dark Casual Shirt', 'brand' => 'Haga Helwa', 'price' => 77, 'stock' => 17, 'image_path' => 'img/products/n8.jpg'],
        ];

        foreach ($products as $product) {
            $payload = $product + [
                'description' => 'Comfortable everyday wear from the summer collection.',
                'is_active' => true,
            ];

            if (Schema::hasColumn('products', 'title')) {
                $payload['title'] = $product['name'];
            }

            if (Schema::hasColumn('products', 'quantity')) {
                $payload['quantity'] = $product['stock'];
            }

            Product::updateOrCreate(
                ['image_path' => $product['image_path']],
                $payload
            );
        }

        $this->seedCoupon('SAVE10', Coupon::TYPE_PERCENT, 10, 0);
        $this->seedCoupon('FLAT25', Coupon::TYPE_FIXED, 25, 100);
    }

    private function seedCoupon(string $code, string $type, int $value, int $minimumOrderAmount): void
    {
        $payload = [
            'type' => $type,
            'value' => $value,
            'minimum_order_amount' => $minimumOrderAmount,
            'is_active' => true,
        ];

        if (Schema::hasColumn('coupons', 'name')) {
            $payload['name'] = $code;
        }

        if (Schema::hasColumn('coupons', 'discount_type')) {
            $payload['discount_type'] = $type === Coupon::TYPE_PERCENT ? 'percentage' : 'fixed';
        }

        if (Schema::hasColumn('coupons', 'discount_value')) {
            $payload['discount_value'] = $value;
        }

        Coupon::updateOrCreate(['code' => $code], $payload);
    }
}

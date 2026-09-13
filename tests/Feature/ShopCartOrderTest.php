<?php

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderPlaced;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Support\Facades\Notification;

test('products are displayed on the shop page', function () {
    $product = Product::factory()->create([
        'name' => 'Linen Summer Shirt',
        'price' => 120,
        'stock' => 7,
    ]);

    $this->get('/shop')
        ->assertOk()
        ->assertSee($product->name)
        ->assertSee('$120.00');
});

test('authenticated users can add products to cart items', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5]);

    $this->actingAs($user)
        ->post(route('cart.store', $product), ['quantity' => 2])
        ->assertRedirect(route('cart.index', absolute: false));

    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

test('users can create orders without coupons', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => 50,
        'stock' => 5,
    ]);

    $this->actingAs($user)
        ->post(route('cart.store', $product), ['quantity' => 2]);

    $this->actingAs($user)
        ->post(route('orders.store'))
        ->assertRedirect();

    $order = Order::with('items')->first();

    expect($order->subtotal)->toEqual('100.00');
    expect($order->discount_total)->toEqual('0.00');
    expect($order->total)->toEqual('100.00');
    expect($order->coupon_code)->toBeNull();
    expect($order->items)->toHaveCount(1);
    expect($product->refresh()->stock)->toBe(3);
    expect($user->cartItems()->count())->toBe(0);
});

test('users can create orders with coupons', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => 100,
        'stock' => 5,
    ]);

    Coupon::create([
        'code' => 'SAVE10',
        'type' => Coupon::TYPE_PERCENT,
        'value' => 10,
        'minimum_order_amount' => 0,
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->post(route('cart.store', $product), ['quantity' => 2]);

    $this->actingAs($user)
        ->post(route('cart.coupon.apply'), ['code' => 'SAVE10'])
        ->assertSessionHasNoErrors();

    $this->actingAs($user)
        ->post(route('orders.store'))
        ->assertRedirect();

    $order = Order::first();

    expect($order->subtotal)->toEqual('200.00');
    expect($order->discount_total)->toEqual('20.00');
    expect($order->total)->toEqual('180.00');
    expect($order->coupon_code)->toBe('SAVE10');
});

test('authenticated admin can create a category and product', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $category = ['name' => 'Apparel'];

    $this->actingAs($admin)->post(route('admin.categories.store'), $category)
        ->assertRedirect()
        ->assertSessionHas('status', 'Category created successfully.');

    $this->assertDatabaseHas('categories', ['name' => 'Apparel']);

    $categoryId = Category::where('name', 'Apparel')->value('id');

    $productData = [
        'name' => 'Admin Product',
        'brand' => 'Test Brand',
        'description' => 'Test description',
        'price' => 10.50,
        'stock' => 5,
        'category_id' => $categoryId,
        'is_active' => 1,
    ];

    $this->actingAs($admin)
        ->post(route('admin.products.store'), $productData)
        ->assertRedirect()
        ->assertSessionHas('status', 'Product created successfully.');

    $this->assertDatabaseHas('products', [
        'name' => 'Admin Product',
        'category_id' => $categoryId,
        'is_active' => true,
    ]);
});

test('guest user can send contact messages', function () {
    $data = [
        'name' => 'Guest User',
        'email' => 'guest@example.com',
        'subject' => 'Question',
        'message' => 'How do I order?',
    ];

    $this->post(route('contact.send'), $data)
        ->assertRedirect()
        ->assertSessionHas('status', 'Your message has been sent to the admin.');

    $this->assertDatabaseHas('contact_messages', [
        'email' => 'guest@example.com',
        'subject' => 'Question',
    ]);
});

test('users can cancel pending orders and receive a status notification', function () {
    Notification::fake();

    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 10, 'stock' => 5]);

    $this->actingAs($user)
        ->post(route('cart.store', $product), ['quantity' => 1]);

    $this->actingAs($user)->post(route('orders.store'));

    $order = Order::first();

    $this->actingAs($user)
        ->patch(route('orders.cancel', $order))
        ->assertRedirect()
        ->assertSessionHas('status', 'Order cancelled successfully.');

    expect($order->refresh()->status)->toBe('cancelled');
    Notification::assertSentTo($user, OrderStatusUpdated::class);
});

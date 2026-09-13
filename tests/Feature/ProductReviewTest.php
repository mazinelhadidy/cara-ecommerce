<?php

use App\Models\Product;
use App\Models\User;
use App\Models\Order;

test('users who ordered a product can submit a review', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5]);

    $this->actingAs($user)
        ->post(route('cart.store', $product), ['quantity' => 1])
        ->assertRedirect(route('cart.index', absolute: false));

    $this->actingAs($user)->post(route('orders.store'));

    $response = $this->actingAs($user)
        ->post(route('products.reviews.store', $product), [
            'rating' => 5,
            'comment' => 'Excellent product',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('product_reviews', [
        'product_id' => $product->id,
        'user_id' => $user->id,
        'rating' => 5,
    ]);
});

test('users who have not ordered the product cannot submit a review', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5]);

    $response = $this->actingAs($user)
        ->post(route('products.reviews.store', $product), [
            'rating' => 4,
            'comment' => 'No order yet',
        ]);

    $response->assertStatus(403);
});

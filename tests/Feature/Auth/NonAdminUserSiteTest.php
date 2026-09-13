<?php

use App\Models\User;

it('redirects admin users away from the regular user site dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/dashboard');

    $response->assertRedirect(route('admin.dashboard'));
});

it('allows normal users to access the dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
});

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless(Auth::check(), 403);

        $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = Auth::user();

        $hasOrdered = $user->orders()
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
            ->exists();

        abort_unless($hasOrdered, 403);

        ProductReview::updateOrCreate([
            'product_id' => $product->id,
            'user_id' => $user->id,
        ], [
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        return back()->with('status', 'Your review was submitted successfully.');
    }
}

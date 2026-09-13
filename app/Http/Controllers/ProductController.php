<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $productsNew = Product::query()
            ->where('is_active', true)
            ->orderBy('created_at','DESC')
            ->get();

        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('created_at')
            ->get();

        return view('Front-End.index', [
            'featuredProducts' => $products->take(8),
            'newArrivalProducts' => $productsNew->take(4),
        ]);
    }

    public function shop(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->paginate(3);

        return view('Front-End.shop', [
            'products' => $products,
        ]);
    }

    public function show(Product $product): View
    {
        $product->load(['reviews' => function ($q) {
            $q->with('user')->latest();
        }]);

        $userHasOrderedProduct = auth()->check() && auth()->user()->orders()
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
            ->exists();

        return view('Front-End.product-show', [
            'product' => $product,
            'userHasOrderedProduct' => $userHasOrderedProduct,
        ]);
    }
}
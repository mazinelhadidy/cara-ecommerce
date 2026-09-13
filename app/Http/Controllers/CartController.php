<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    private const COUPON_SESSION_KEY = 'cart_coupon_code';

    public function index(): View
    {
        $cartItems = $this->cartItems();
        $subtotal = $this->subtotal($cartItems);
        $coupon = $this->availableCoupon(session(self::COUPON_SESSION_KEY), $subtotal);
        $discount = $coupon?->discountFor($subtotal) ?? 0.0;

        if (session(self::COUPON_SESSION_KEY) && ! $coupon) {
            session()->forget(self::COUPON_SESSION_KEY);
        }

        return view('Front-End.cart', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'coupon' => $coupon,
            'discount' => $discount,
            'total' => max($subtotal - $discount, 0),
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if (! $product->is_active || $product->stock < 1) {
            return back()->withErrors(['cart' => 'This product is not available right now.']);
        }

        $cartItem = CartItem::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
        ]);

        $newQuantity = ($cartItem->exists ? $cartItem->quantity : 0) + (int) $data['quantity'];

        if ($newQuantity > $product->stock) {
            return back()->withErrors([
                'quantity' => "Only {$product->stock} items are available for {$product->name}.",
            ]);
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        return redirect()
            ->route('cart.index')
            ->with('status', "{$product->name} was added to your cart.");
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->authorizeCartItem($cartItem);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ((int) $data['quantity'] > $cartItem->product->stock) {
            return back()->withErrors([
                'quantity' => "Only {$cartItem->product->stock} items are available for {$cartItem->product->name}.",
            ]);
        }

        $cartItem->update(['quantity' => (int) $data['quantity']]);

        return back()->with('status', 'Cart updated.');
    }

    public function destroy(CartItem $cartItem): RedirectResponse
    {
        $this->authorizeCartItem($cartItem);

        $cartItem->delete();

        return back()->with('status', 'Product removed from cart.');
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $subtotal = $this->subtotal($this->cartItems());
        $coupon = $this->availableCoupon($data['code'], $subtotal);

        if (! $coupon) {
            return back()->withErrors([
                'coupon' => 'Coupon code is invalid, expired, inactive, or below the minimum cart total.',
            ]);
        }

        session([self::COUPON_SESSION_KEY => $coupon->code]);

        return back()->with('status', "Coupon {$coupon->code} applied.");
    }

    public function removeCoupon(): RedirectResponse
    {
        session()->forget(self::COUPON_SESSION_KEY);

        return back()->with('status', 'Coupon removed.');
    }

    private function authorizeCartItem(CartItem $cartItem): void
    {
        abort_unless($cartItem->user_id === Auth::id(), 403);
    }

    private function cartItems(): Collection
    {
        return Auth::user()
            ->cartItems()
            ->with('product')
            ->oldest()
            ->get();
    }

    private function subtotal(Collection $cartItems): float
    {
        return round($cartItems->sum(fn (CartItem $item) => $item->lineTotal()), 2);
    }

    private function availableCoupon(?string $code, float $subtotal): ?Coupon
    {
        if (! $code) {
            return null;
        }

        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        return $coupon?->isAvailableFor($subtotal) ? $coupon : null;
    }
}

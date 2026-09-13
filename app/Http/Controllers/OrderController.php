<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\OrderPlaced;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const COUPON_SESSION_KEY = 'cart_coupon_code';

    public function index(): View
    {
        $orders = Auth::user()
            ->orders()
            ->latest()
            ->paginate(12);

        return view('Front-End.orders', [
            'orders' => $orders,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cartItems = $this->cartItems();

        if ($cartItems->isEmpty()) {
            return back()->withErrors(['cart' => 'Your cart is empty.']);
        }

        $subtotal = $this->subtotal($cartItems);
        $coupon = $this->couponForCheckout($request, $subtotal);

        if (($request->filled('coupon_code') || session(self::COUPON_SESSION_KEY)) && ! $coupon) {
            return back()->withErrors([
                'coupon' => 'Coupon code is invalid, expired, inactive, or below the minimum cart total.',
            ]);
        }

        $discount = $coupon?->discountFor($subtotal) ?? 0.0;
        $user = Auth::user();

        $order = DB::transaction(function () use ($cartItems, $coupon, $discount, $subtotal, $user): Order {
            $order = Order::create([
                'user_id' => $user->id,
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'subtotal' => $subtotal,
                'discount_total' => $discount,
                'total' => max($subtotal - $discount, 0),
                'status' => 'pending',
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'shipping_address' => $user->address,
            ]);

            foreach ($cartItems as $cartItem) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($cartItem->product_id);

                if ($cartItem->quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => "Only {$product->stock} items are available for {$product->name}.",
                    ]);
                }

                $lineTotal = round((float) $product->price * $cartItem->quantity, 2);

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $cartItem->quantity,
                    'line_total' => $lineTotal,
                ]);

                $product->decrement('stock', $cartItem->quantity);
            }

            $user->cartItems()->delete();

            return $order;
        });

        session()->forget(self::COUPON_SESSION_KEY);

        $user->notify(new OrderPlaced($order));

        return redirect()
            ->route('orders.show', $order)
            ->with('status', 'Order placed successfully.');
    }

    public function show(Order $order): View
    {
        abort_unless($order->user_id === Auth::id(), 403);

        return view('Front-End.order', [
            'order' => $order->load('items', 'coupon'),
        ]);
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === Auth::id(), 403);

        if ($order->status !== 'pending') {
            return back()->withErrors(['order' => 'Only pending orders can be cancelled.']);
        }

        $order->update(['status' => 'cancelled']);
        $order->user->notify(new OrderStatusUpdated($order));

        return back()->with('status', 'Order cancelled successfully.');
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

    private function couponForCheckout(Request $request, float $subtotal): ?Coupon
    {
        $code = $request->filled('coupon_code')
            ? $request->input('coupon_code')
            : session(self::COUPON_SESSION_KEY);

        if (! $code) {
            return null;
        }

        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        return $coupon?->isAvailableFor($subtotal) ? $coupon : null;
    }
}

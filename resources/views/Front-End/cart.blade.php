@include('Front-End.header')
@include('Front-End.navbar')

<section id="page-header">
    <h2>Your Cart</h2>
    <p>Review quantities, apply coupons, and place your order.</p>
</section>

@include('Front-End.alerts')

<section id="cart" class="section-p1">
    @if ($cartItems->isEmpty())
        <div class="empty-state">
            Your cart is empty.
            <a href="{{ route('shop.index') }}">Continue shopping</a>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <td>Remove</td>
                    <td>Image</td>
                    <td>Product</td>
                    <td>Price</td>
                    <td>Quantity</td>
                    <td>Subtotal</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($cartItems as $item)
                    <tr>
                        <td>
                            <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="icon-button" title="Remove item">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <img src="{{ $item->product->image_path ? asset($item->product->image_path) : asset('img/products/f1.jpg') }}" alt="{{ $item->product->name }}">
                        </td>
                        <td>{{ $item->product->name }}</td>
                        <td>${{ number_format($item->product->price, 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('cart.update', $item) }}" class="quantity-form">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}">
                                <button type="submit">Update</button>
                            </form>
                        </td>
                        <td>${{ number_format($item->lineTotal(), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>

<section id="cart-add" class="section-p1">
    <div id="coupon">
        <h3>Apply Coupon</h3>
        @if ($coupon)
            <p>Coupon <strong>{{ $coupon->code }}</strong> is applied.</p>
            <form method="POST" action="{{ route('cart.coupon.remove') }}">
                @csrf
                @method('DELETE')
                <button type="submit">Remove Coupon</button>
            </form>
        @else
            <form method="POST" action="{{ route('cart.coupon.apply') }}">
                @csrf
                <input type="text" name="code" placeholder="Enter coupon code" value="{{ old('code') }}">
                <button type="submit" @disabled($cartItems->isEmpty())>Apply</button>
            </form>
        @endif
    </div>

    <div id="subTotal">
        <h3>Cart Totals</h3>
        <table>
            <tr>
                <td>Cart Subtotal</td>
                <td>${{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>Discount</td>
                <td>-${{ number_format($discount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>${{ number_format($total, 2) }}</strong></td>
            </tr>
        </table>
        <form method="POST" action="{{ route('orders.store') }}">
            @csrf
            <button type="submit" @disabled($cartItems->isEmpty())>Place Order</button>
        </form>
    </div>
</section>

@include('Front-End.footer')

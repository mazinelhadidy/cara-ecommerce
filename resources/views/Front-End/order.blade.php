@include('Front-End.header')
@include('Front-End.navbar')

<section id="page-header">
    <h2>Order #{{ $order->id }}</h2>
    <p>Your order has been received.</p>
</section>

@include('Front-End.alerts')

<section id="cart" class="section-p1">
    <table>
        <thead>
            <tr>
                <td>Product</td>
                <td>Price</td>
                <td>Quantity</td>
                <td>Subtotal</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>

<section id="cart-add" class="section-p1">
    <div id="coupon">
        <h3>Order Details</h3>
        <p>Status: <strong>{{ ucfirst($order->status) }}</strong></p>
        <p>Customer: {{ $order->customer_name }}</p>
        <p>Email: {{ $order->customer_email }}</p>
    </div>

    <div id="subTotal">
        <h3>Order Totals</h3>
        <table>
            <tr>
                <td>Subtotal</td>
                <td>${{ number_format($order->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>Coupon</td>
                <td>{{ $order->coupon_code ?? 'None' }}</td>
            </tr>
            <tr>
                <td>Discount</td>
                <td>-${{ number_format($order->discount_total, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>${{ number_format($order->total, 2) }}</strong></td>
            </tr>
        </table>
        <a href="{{ route('shop.index') }}" class="checkout-link">Continue Shopping</a>
    </div>
</section>

@include('Front-End.footer')

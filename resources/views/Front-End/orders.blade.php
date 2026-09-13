@include('Front-End.header')
@include('Front-End.navbar')

<section class="section-p1">
    <h2>My Orders</h2>

    @include('Front-End.alerts')

    <div class="orders-table">
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><a href="{{ route('orders.show', $order) }}">#{{ $order->id }}</a></td>
                        <td>{{ ucfirst($order->status) }}</td>
                        <td>${{ number_format($order->total, 2) }}</td>
                        <td>{{ $order->created_at->format('M j, Y') }}</td>
                        <td>
                            @if($order->status === 'pending')
                                <form method="POST" action="{{ route('orders.cancel', $order) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="normal">Cancel</button>
                                </form>
                            @else
                                <span class="order-label">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">You have not placed any orders yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $orders->links() }}
        </div>
    </div>
</section>

@include('Front-End.footer')

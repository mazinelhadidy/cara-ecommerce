@include('Front-End.header')
@include('Front-End.navbar')

<section id="product1" class="section-p1">
    <h2>Shop</h2>
    <p>Browse all available products.</p>
    <div class="pro-container">
        @forelse($products as $product)
            @include('Front-End.product-card', ['product' => $product])
        @empty
            <div class="empty-state">No products found.</div>
        @endforelse
    </div>

    <div class="pagination-wrapper">
        {{ $products->links() }}
    </div>
</section>

@include('Front-End.footer')

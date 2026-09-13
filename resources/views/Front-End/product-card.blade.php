<div class="pro">
    <div class="img-wrap">
        <a href="{{ route('products.show', $product) }}" class="product-link">
            <img src="{{ $product->image_path ? asset($product->image_path) : asset('img/products/f1.jpg') }}" alt="{{ $product->name }}">
        </a>
    </div>
    <div class="des">
        <span>{{ $product->brand }}</span>
        <h5><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h5>
        <div class="star" aria-hidden="true">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <div class="card-footer">
            <div class="price">${{ number_format($product->price, 2) }}</div>
            <div class="meta-and-actions">
                <div class="product-meta">Stock: {{ $product->stock }}</div>
                <form method="POST" action="{{ route('cart.store', $product) }}" class="add-cart-form">
                    @csrf
                    <input
                        type="number"
                        name="quantity"
                        value="1"
                        min="1"
                        max="{{ max($product->stock, 1) }}"
                        aria-label="Quantity for {{ $product->name }}"
                        @disabled($product->stock < 1)
                    >
                    <button type="submit" class="cart" title="Add to cart" @disabled($product->stock < 1)>
                        <i class="fas fa-shopping-cart"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


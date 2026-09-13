@include('Front-End.header')
@include('Front-End.navbar')

<section id="product-detail" class="section-p1">
    <div class="product-detail-card">
        <div class="product-detail-image">
            <img src="{{ $product->image_path ? asset($product->image_path) : asset('img/products/f1.jpg') }}" alt="{{ $product->name }}">
        </div>
        <div class="product-detail-info">
            <span>{{ $product->brand }}</span>
            <h1>{{ $product->name }}</h1>
            <div class="star">
                @for ($i = 0; $i < 5; $i++)
                    <i class="fas fa-star"></i>
                @endfor
            </div>
            <h2>${{ number_format($product->price, 2) }}</h2>
            <p>{{ $product->description }}</p>
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

    @include('Front-End.product-reviews', [
        'product' => $product,
        'userHasOrderedProduct' => $userHasOrderedProduct,
    ])
</section>

@include('Front-End.footer')
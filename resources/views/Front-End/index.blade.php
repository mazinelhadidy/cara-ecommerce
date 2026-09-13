@include('Front-End.header')
@include('Front-End.navbar')

<section id="hero">
    <h4>Trade-in-offer</h4>
    <h2>Super value deals</h2>
    <h1>On all products</h1>
    <p>Save more with coupons and up to 70% off!</p>
    <a href="#product1" class="hero-button">Shop Now!</a>
</section>

@include('Front-End.alerts')

<section id="feature" class="section-p1">
    <div class="fe-1">
        <img src="{{ asset('img/features/f1.png') }}" alt="">
        <h6>Free Shipping</h6>
    </div>
    <div class="fe-1">
        <img src="{{ asset('img/features/f2.png') }}" alt="">
        <h6>Online Order</h6>
    </div>
    <div class="fe-1">
        <img src="{{ asset('img/features/f3.png') }}" alt="">
        <h6>Save Money</h6>
    </div>
    <div class="fe-1">
        <img src="{{ asset('img/features/f4.png') }}" alt="">
        <h6>Promotions</h6>
    </div>
    <div class="fe-1">
        <img src="{{ asset('img/features/f5.png') }}" alt="">
        <h6>Happy Sell</h6>
    </div>
    <div class="fe-1">
        <img src="{{ asset('img/features/f6.png') }}" alt="">
        <h6>24/7 Support</h6>
    </div>
</section>

<section id="product1" class="section-p1">
    <h2>Featured Products</h2>
    <p>Summer Collection New Modern Design</p>
    <div class="pro-container">
        @forelse ($featuredProducts as $product)
            @include('Front-End.product-card', ['product' => $product])
        @empty
            <div class="empty-state">No products are available yet.</div>
        @endforelse
    </div>
</section>

<section id="bannar" class="section-m1">
    <h4>Repair Service</h4>
    <h2>Up to <span>70% Off</span> - All T-Shirts & Accessories</h2>
    <a href="{{ route('shop.index') }}" class="normal banner-link">Explore More</a>
</section>

<section id="product1" class="section-p1">
    <h2>New Arrival</h2>
    <p>Fresh pieces from the latest collection</p>
    <div class="pro-container">
        @forelse ($newArrivalProducts as $product)
            @include('Front-End.product-card', ['product' => $product])
        @empty
            <div class="empty-state">More arrivals are coming soon.</div>
        @endforelse
    </div>
</section>

<section id="sm-bannar" class="section-p1">
    <div class="bannar-box">
        <h4>Crazy Deals</h4>
        <h2>buy 1 get 1 Free</h2>
        <span>The best classic dress is on sale from Cara</span>
        <a href="{{ route('shop.index') }}" class="white banner-link">Learn More</a>
    </div>
    <div class="bannar-box bannar-box2">
        <h4>Spring/Summer</h4>
        <h2>buy 1 get 1 Free</h2>
        <span>The best classic dress is on sale from Cara</span>
        <a href="{{ route('shop.index') }}" class="white banner-link">Learn More</a>
    </div>
</section>

<section id="bannar3" class="section-p1">
    <div class="bannar-box">
        <h2>SEASONAL SALE</h2>
        <h3>Winter Collection - 50% off</h3>
    </div>
    <div class="bannar-box bannar-box2">
        <h2>SEASONAL SALE</h2>
        <h3>Winter Collection - 50% off</h3>
    </div>
    <div class="bannar-box bannar-box3">
        <h2>SEASONAL SALE</h2>
        <h3>Winter Collection - 50% off</h3>
    </div>
</section>

<section id="newsletter" class="section-p1 section-m1">
    <div class="newstext">
        <h4>Sign Up For Newsletters</h4>
        <p>Get e-mail updates about our latest shop and <span class="text-warning">special offers.</span></p>
    </div>
    <div class="form">
        <input type="email" placeholder="Enter your e-mail...">
        <button class="normal">Sign Up</button>
    </div>
</section>

@include('Front-End.footer')

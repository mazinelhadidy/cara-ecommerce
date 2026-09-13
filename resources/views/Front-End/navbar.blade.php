<section id="header">
    <a href="{{ route('home') }}">
        <img src="{{ asset('img/logo.png') }}" alt="Haga Helwa">
    </a>

    <div>
        <ul id="navbar">
            <li class="link">
                <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
            </li>
            <li class="link">
                <a class="{{ request()->routeIs('shop.index') ? 'active' : '' }}" href="{{ route('shop.index') }}">Shop</a>
            </li>
            <li class="link">
                <a class="{{ request()->routeIs('blog') ? 'active' : '' }}" href="{{ route('blog') }}">Blog</a>
            </li>
            <li class="link">
                <a class="{{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
            </li>
            <li class="link">
                <a href="{{ route('contact') }}">Contact</a>
            </li>
            @guest
                <li class="link">
                    <a href="{{ route('login') }}">Login</a>
                </li>
                <li class="link">
                    <a href="{{ route('register') }}">Signup</a>
                </li>
            @else
                <li class="link">
                    <form method="POST" action="{{ route('logout') }}" class="nav-logout-form">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                </li>
            @endguest
            <li class="link">
                <a id="lg-cart" href="{{ route('cart.index') }}">
                    <i class="fas fa-shopping-cart"></i>
                    @auth
                        @php($cartCount = auth()->user()->cartItems()->sum('quantity'))
                        @if($cartCount > 0)
                            <span class="cart-count">{{ $cartCount }}</span>
                        @endif
                    @endauth
                </a>
            </li>
            <a href="#" id="close"><i class="fas fa-times"></i> </a>
        </ul>

    </div>

    <div id="mobile">
        <a href="{{ route('cart.index') }}">
            <i class="fas fa-shopping-cart"></i>
        </a>
        <a href="#" id="bar"> <i class="fas fa-outdent"></i> </a>
    </div>
</section>

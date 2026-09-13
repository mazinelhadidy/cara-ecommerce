<footer class="section-p1">
        <div class="col">
            <a href="{{ route('home') }}"><img class="logo" src="{{ asset('img/logo.png') }}" alt="Haga Helwa"></a>
            <h4>Contact</h4>
            <p><strong>Address: </strong>526 manchster Road, street 32, manchster</p>
            <p><strong>Phone: </strong>0106244875</p>
            <p><strong>Hourse: </strong>10AM - 10Pm, Sat- thus</p>
            <div class="follow">
                <h4>Follow US :</h4>
                <div class="icon">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

        </div>
        <div class="col">
            <h4>About Us</h4>
            <a href="#">About Us</a>
            <a href="#">Delivery information</a>
            <a href="#">Privacy policy</a>
            <a href="#">Terms & Condtions</a>
            <a href="{{ route('contact') }}">Contact Us</a>
        </div>
        <div class="col">
            <h4>My Account</h4>
            <a href="#">Sign in</a>
            <a href="#">View Cart</a>
            <a href="#">My Whishlist</a>
            <a href="{{ route('orders.index') }}">Track My order</a>
            <a href="#">Help</a>
        </div>
        <div class="col install">
            <h4>Install App</h4>
            <p>From App Store Or Google Play</p>
            <div class="oo">
                <img src="{{ asset('img/pay/app.jpg') }}" alt="App Store">
                <img src="{{ asset('img/pay/play.jpg') }}" alt="Google Play">
            </div>
            <p>Secure payment For your happy Service</p>
            <img src="{{ asset('img/pay/pay.png') }}" alt="Payment methods">
        </div>

        <div class="copyright">
            <p> all The right reserved for OmarTurbo, 2022</p>
        </div>
    </footer>


    <script src="{{ asset('js/main.js') }}?v={{ filemtime(public_path('js/main.js')) }}"></script>
</body>

</html>

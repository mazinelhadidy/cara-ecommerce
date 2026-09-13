@if (session('status'))
    <div class="shop-alert shop-alert-success section-p1">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="shop-alert shop-alert-error section-p1">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="product-reviews">
    <h3>Reviews</h3>

    @if ($product->reviews->isEmpty())
        <p class="no-reviews">Be the first to review this product.</p>
    @else
        <div class="review-list">
            @foreach ($product->reviews as $review)
                <div class="review-item">
                    <div class="review-header">
                        <strong>{{ $review->user->name }}</strong>
                        <span class="rating">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                        <span class="review-date">{{ $review->created_at->format('M j, Y') }}</span>
                    </div>
                    @if ($review->comment)
                        <p>{{ $review->comment }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @auth
        <form method="POST" action="{{ route('products.reviews.store', $product) }}" class="review-form">
            @csrf

            <div>
                <label for="rating">Rating</label>
                <input type="hidden" id="rating" name="rating" value="{{ old('rating', 1) }}" required>
                <div class="rating-picker" role="radiogroup" aria-label="Product rating">
                    @foreach(range(1,5) as $value)
                        <button type="button" class="star-btn" data-value="{{ $value }}" aria-pressed="{{ old('rating', 1) >= $value ? 'true' : 'false' }}">
                            <i class="fas fa-star"></i>
                        </button>
                    @endforeach
                </div>
                @error('rating')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <br></br>
                <label for="comment">Comment</label>
                <br></br>

                <textarea id="comment" name="comment" rows="4">{{ old('comment') }}</textarea>
                @error('comment')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit">Submit Review</button>
        </form>
        <p class="review-note">Note: only customers who ordered this product can have reviews accepted.</p>
    @else
        <p class="review-note">Please <a href="{{ route('login') }}">login</a> to leave a review.</p>
    @endauth

</div>

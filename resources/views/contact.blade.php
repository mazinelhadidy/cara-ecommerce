@include('Front-End.header')
@include('Front-End.navbar')

<section class="section-p1">
    <div class="contact-wrapper">
        <h2>Contact Us</h2>
        <p>Send a message to the store admin and we will reply as soon as possible.</p>

        @include('Front-End.alerts')

        <form method="POST" action="{{ route('contact.send') }}" class="contact-form">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label for="subject">Subject</label>
                <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
            </div>
            <button type="submit" class="normal">Send Message</button>
        </form>
    </div>
</section>

@include('Front-End.footer')

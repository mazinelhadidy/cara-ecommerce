const navBar = document.getElementById('navbar');
const openButton = document.getElementById('bar');
const closeButton = document.getElementById('close');

if (openButton && navBar) {
    openButton.addEventListener('click', (event) => {
        event.preventDefault();
        navBar.classList.add('active');
    });
}

// Rating picker: make stars clickable and set hidden rating input
document.addEventListener('DOMContentLoaded', function () {
    const pickers = document.querySelectorAll('.rating-picker');
    pickers.forEach(picker => {
        const hidden = picker.closest('form')?.querySelector('input[name="rating"]');
        if (!hidden) return;
        const buttons = picker.querySelectorAll('.star-btn');
        const setRating = (value) => {
            hidden.value = value;
            buttons.forEach(btn => {
                const v = parseInt(btn.getAttribute('data-value'));
                btn.setAttribute('aria-pressed', v <= value ? 'true' : 'false');
            });
        };

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const v = parseInt(btn.getAttribute('data-value'));
                setRating(v);
            });
            btn.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    btn.click();
                }
            });
        });

        // initialize from hidden value
        setRating(parseInt(hidden.value) || 1);
    });
});

if (closeButton && navBar) {
    closeButton.addEventListener('click', (event) => {
        event.preventDefault();
        navBar.classList.remove('active');
    });
}

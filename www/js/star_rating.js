document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.star-rating');
    const stars = Array.from(container.querySelectorAll('.star'));
    const hiddenInput = document.querySelector('input[name="rating"]');

    stars.forEach(star => {
        const value = parseInt(star.dataset.value);

        star.addEventListener('click', () => {
            hiddenInput.value = value;
            console.log('set:', hiddenInput.value);
            stars.forEach(s => {
                s.classList.toggle('selected', parseInt(s.dataset.value) <= value);
            });
        });

        star.addEventListener('mouseenter', () => {
            stars.forEach(s => {
                s.classList.toggle('hovered', parseInt(s.dataset.value) <= value);
            });
        });

        star.addEventListener('mouseleave', () => {
            stars.forEach(s => s.classList.remove('hovered'));
        });
    });
});
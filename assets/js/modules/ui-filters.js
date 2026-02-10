document.querySelectorAll('.filters-container select').forEach(select => {

    select.addEventListener('change', () => {

        if (select.value !== 'all') {
            select.classList.add('is-active');
        } else {
            select.classList.remove('is-active');
        }

    });

});

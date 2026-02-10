document.addEventListener('DOMContentLoaded', () => {

    // ======= CONFIG =======
    const postsGrid = document.querySelector('.posts-grid');
    const paginationContainer = document.querySelector('.pagination');
    const postsPerPage = 8;

    if (!postsGrid || !paginationContainer) return;

    // Sélectionne uniquement les items visibles dans la grille
    let posts = Array.from(postsGrid.children);

    let currentPage = 1;
    let filteredPosts = [...posts]; // pour appliquer les filtres

    // ======= FUNCTIONS =======

    function displayPosts(postsToShow, page = 1) {
        posts.forEach(p => p.style.display = 'none');

        const start = (page - 1) * postsPerPage;
        const end = start + postsPerPage;

        postsToShow.slice(start, end).forEach(p => {
            p.style.display = '';
        });
    }

    function createPagination(postsToShow) {
        paginationContainer.innerHTML = '';
        const totalPages = Math.ceil(postsToShow.length / postsPerPage);

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.innerHTML = `<a href="#">${i}</a>`;
            if (i === currentPage) li.classList.add('active');

            li.addEventListener('click', e => {
                e.preventDefault();
                currentPage = i;
                displayPosts(postsToShow, currentPage);

                Array.from(paginationContainer.children).forEach(c => c.classList.remove('active'));
                li.classList.add('active');
            });

            paginationContainer.appendChild(li);
        }
    }

    // ======= FILTERS =======
    const filterElements = document.querySelectorAll('#filter-media, #filter-genre, #filter-theme, #filter-nation');

    filterElements.forEach(select => {
        select.addEventListener('change', () => {
            // Appliquer les filtres sur les data-* de chaque post
            filteredPosts = posts.filter(post => {
                let show = true;

                filterElements.forEach(f => {
                    const value = f.value;
                    if (value === 'all') return;

                    const dataAttr = f.id.replace('filter-', ''); // media, genre, theme, nation
                    const postData = post.dataset[dataAttr]; // récupère data-media, data-genre, etc.

                    if (!postData) return;

                    // Plusieurs slugs possibles (thèmes)
                    if (!postData.split(' ').includes(value)) {
                        show = false;
                    }
                });

                return show;
            });

            // reset page
            currentPage = 1;
            displayPosts(filteredPosts, currentPage);
            createPagination(filteredPosts);
        });
    });

    // ======= INITIAL DISPLAY =======
    displayPosts(filteredPosts, currentPage);
    createPagination(filteredPosts);

});

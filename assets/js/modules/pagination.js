document.addEventListener('DOMContentLoaded', () => {
    const posts = Array.from(document.querySelectorAll('.post-box, .chronique-card'));
    const paginationContainer = document.querySelector('.pagination');
    
    if (!paginationContainer || posts.length === 0) {
        return;
    }
    
    const postsPerPage = 6;
    let currentPage = 1;

    function displayPosts(postsToShow, page = 1) {
        // Cache tous les posts
        posts.forEach(p => p.style.display = 'none');
        
        // Affiche seulement ceux de la page actuelle
        const start = (page - 1) * postsPerPage;
        const end = start + postsPerPage;
        postsToShow.slice(start, end).forEach(p => {
            // Retire le style inline pour revenir au CSS par défaut
            p.style.display = '';
        });
    }

    function createPagination(postsToShow) {
        paginationContainer.innerHTML = '';
        const totalPages = Math.ceil(postsToShow.length / postsPerPage);

        for(let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.innerHTML = `<a href="#">${i}</a>`;
            if(i === currentPage) li.classList.add('active');

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

    displayPosts(posts, currentPage);
    createPagination(posts);
});
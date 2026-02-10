document.addEventListener('DOMContentLoaded', () => {
    const posts = [...document.querySelectorAll('.post-box')];
    const pagination = document.querySelector('.pagination');
    const filters = ['filter-genre', 'filter-theme', 'filter-nation', 'filter-media']
        .map(id => document.getElementById(id));

    if (filters.includes(null) || !pagination) return;

    const postsPerPage = 8;
    let currentPage = 1;

    const getFiltered = () => {
        const [genre, theme, nation, media] = filters.map(f => f.value);
        return posts.filter(post => {
            const pGenre = post.dataset.genre;
            const pThemes = post.dataset.theme?.split(' ') || [];
            const pNation = post.dataset.nation;
            const pMedia = post.dataset.media;

            return (genre === 'all' || pGenre === genre)
                && (theme === 'all' || pThemes.includes(theme))
                && (nation === 'all' || pNation === nation)
                && (media === 'all' || pMedia === media);
        });
    };

    const showPosts = (items) => {
        posts.forEach(p => p.style.display = 'none');
        items.forEach(p => p.style.display = 'flex');
    };

    const paginate = (items) => {
        pagination.innerHTML = '';
        const totalPages = Math.ceil(items.length / postsPerPage);

        [...Array(totalPages)].forEach((_, i) => {
            const page = i + 1;
            const li = document.createElement('li');
            li.innerHTML = `<a href="#">${page}</a>`;
            li.classList.toggle('active', page === currentPage);
            
            li.onclick = (e) => {
                e.preventDefault();
                currentPage = page;
                apply();
            };

            pagination.appendChild(li);
        });
    };

    function apply() {
        const filtered = getFiltered();
        const start = (currentPage - 1) * postsPerPage;
        const visible = filtered.slice(start, start + postsPerPage);

        paginate(filtered);
        showPosts(visible);
    }

    filters.forEach(f => f.onchange = () => {
        currentPage = 1;
        apply();
    });

    apply();
});

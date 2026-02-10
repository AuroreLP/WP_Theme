document.addEventListener("DOMContentLoaded", () => {
    const posts = document.querySelectorAll(".post-box");
    const filterSelect = document.getElementById("filter-category");
    const paginationContainer = document.querySelector(".pagination");

    const postsPerPage = 8;
    let currentPage = 1;
    let filteredPosts = [...posts];

    function showPosts() {
        posts.forEach(post => post.style.display = "none");

        let start = (currentPage - 1) * postsPerPage;
        let end = start + postsPerPage;
        let pagePosts = filteredPosts.slice(start, end);

        pagePosts.forEach(post => post.style.display = "flex");

        generatePagination();
    }

    function generatePagination() {
        paginationContainer.innerHTML = "";

        let totalPages = Math.ceil(filteredPosts.length / postsPerPage);

        for (let i = 1; i <= totalPages; i++) {
            let li = document.createElement("li");
            li.classList.add("page-item");

            
            li.innerHTML = `<a href="#" class="page-link">${i}</a>`;

            if (i === currentPage) li.classList.add("active");

            li.querySelector('a').addEventListener('click', (e) => {
                e.preventDefault(); 
                currentPage = i;
                showPosts();
                scrollToTop();
            });

            paginationContainer.appendChild(li);
        }
    }

    function filterPosts() {
        const selectedCategory = filterSelect.value;

        filteredPosts = [...posts].filter(post => {
            const postCategory = post.getAttribute("data-category");
            return selectedCategory === "all" || postCategory === selectedCategory;
        });

        currentPage = 1;
        showPosts();
    }

    function scrollToTop() {
        const topOffset = document.querySelector(".heading")?.offsetTop || 0;
        window.scrollTo({ top: topOffset - 50, behavior: "smooth" });
    }

    filterSelect.addEventListener("change", filterPosts);

    showPosts();
});

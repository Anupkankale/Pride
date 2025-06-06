class PrideBlogUI {
    constructor(container, api) {
        this.container = container;
        this.api = api;
        this.postsContainer = container.find('.pride-blog-grid, .pride-blog-list');
        this.searchInput = container.find('.pride-blog-search input');
        this.sortSelect = container.find('.pride-blog-sort select');
        this.categoryFilters = container.find('.pride-blog-filter-checkbox');
        this.pagination = container.find('.pride-blog-pagination');
        this.loadingSpinner = $('<div class="pride-blog-loading"><div class="pride-blog-loading-spinner"></div></div>');
        
        this.currentPage = 1;
        this.isLoading = false;
        this.settings = this.getContainerSettings();

        this.initializeEventListeners();
        this.loadPosts();
    }

    /**
     * Get settings from container data attributes
     * @returns {Object} Container settings
     */
    getContainerSettings() {
        return {
            postsPerPage: this.container.data('posts-per-page') || 9,
            layout: this.container.data('layout') || 'grid',
            category: this.container.data('category') || '',
            order: this.container.data('order') || 'DESC',
            orderby: this.container.data('orderby') || 'date'
        };
    }

    /**
     * Initialize event listeners
     */
    initializeEventListeners() {
        // Search input handler with debounce
        let searchTimeout;
        this.searchInput.on('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.currentPage = 1;
                this.loadPosts({ search: this.searchInput.val() });
            }, 500);
        });

        // Sort select handler
        this.sortSelect.on('change', () => {
            this.currentPage = 1;
            const [orderby, order] = this.sortSelect.val().split('-');
            this.loadPosts({ orderby, order });
        });

        // Category filter handler
        this.categoryFilters.on('change', () => {
            this.currentPage = 1;
            const selectedCategories = this.categoryFilters
                .filter(':checked')
                .map(function() {
                    return $(this).val();
                })
                .get()
                .join(',');

            this.loadPosts({ categories: selectedCategories });
        });

        // Pagination handler
        this.container.on('click', '.pride-blog-pagination button', (e) => {
            const button = $(e.currentTarget);
            
            if (button.hasClass('pagination-prev')) {
                this.currentPage--;
            } else if (button.hasClass('pagination-next')) {
                this.currentPage++;
            } else {
                this.currentPage = parseInt(button.text());
            }

            this.loadPosts();
            this.scrollToTop();
        });
    }

    /**
     * Load posts with given parameters
     * @param {Object} params Additional query parameters
     */
    async loadPosts(params = {}) {
        if (this.isLoading) return;
        this.isLoading = true;

        // Show loading spinner
        this.postsContainer.html(this.loadingSpinner);

        try {
            const { posts, totalPages } = await this.api.fetchPosts({
                page: this.currentPage,
                posts_per_page: this.settings.postsPerPage,
                ...this.settings,
                ...params
            });

            // Update container
            this.postsContainer.empty();

            if (posts.length === 0) {
                this.postsContainer.html('<div class="pride-blog-no-posts">No posts found</div>');
                return;
            }

            // Render posts
            posts.forEach(post => {
                const formattedPost = this.api.formatPostData(post);
                const postElement = this.createPostElement(formattedPost);
                this.postsContainer.append(postElement);
            });

            // Update pagination
            this.updatePagination(this.currentPage, totalPages);

        } catch (error) {
            console.error('Error loading posts:', error);
            this.postsContainer.html('<div class="pride-blog-no-posts">Error loading posts. Please try again.</div>');
        } finally {
            this.isLoading = false;
        }
    }

    /**
     * Create post element
     * @param {Object} post Formatted post data
     * @returns {jQuery} Post element
     */
    createPostElement(post) {
        return $(`
            <article class="pride-blog-post">
                ${post.featuredMedia ? `
                    <div class="pride-blog-thumbnail">
                        <img src="${post.featuredMedia}" alt="${post.title}">
                    </div>
                ` : ''}
                
                <div class="pride-blog-content">
                    <div class="pride-blog-categories">
                        ${post.categories.map(cat => `
                            <a href="/category/${cat.slug}" class="pride-blog-category">
                                ${cat.name}
                            </a>
                        `).join('')}
                    </div>

                    <h2 class="pride-blog-title">
                        <a href="${post.link}">${post.title}</a>
                    </h2>

                    <div class="pride-blog-excerpt">
                        ${post.excerpt}
                    </div>

                    <div class="pride-blog-meta">
                        <div class="pride-blog-author">
                            <img src="${post.author.avatar}" alt="${post.author.name}">
                            ${post.author.name}
                        </div>
                        <div class="pride-blog-date">
                            ${post.date}
                        </div>
                    </div>

                    <a href="${post.link}" class="pride-blog-read-more">
                        Read More
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </article>
        `);
    }

    /**
     * Update pagination
     * @param {number} currentPage Current page number
     * @param {number} totalPages Total number of pages
     */
    updatePagination(currentPage, totalPages) {
        this.pagination.empty();

        if (totalPages <= 1) return;

        // Previous button
        if (currentPage > 1) {
            this.pagination.append(`
                <button class="pagination-prev">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            `);
        }

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === currentPage) {
                this.pagination.append(`<button class="active">${i}</button>`);
            } else if (
                i === 1 ||
                i === totalPages ||
                (i >= currentPage - 1 && i <= currentPage + 1)
            ) {
                this.pagination.append(`<button>${i}</button>`);
            } else if (
                i === currentPage - 2 ||
                i === currentPage + 2
            ) {
                this.pagination.append('<span>...</span>');
            }
        }

        // Next button
        if (currentPage < totalPages) {
            this.pagination.append(`
                <button class="pagination-next">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            `);
        }
    }

    /**
     * Scroll to top of blog container
     */
    scrollToTop() {
        $('html, body').animate({
            scrollTop: this.container.offset().top - 50
        }, 500);
    }
} 
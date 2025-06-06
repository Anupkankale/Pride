class PrideBlogAPI {
    constructor() {
        this.baseUrl = '/wp-json/wp/v2';
        this.defaultParams = {
            _embed: true // Include featured media, author, and categories in response
        };
    }

    /**
     * Fetch posts with given parameters
     * @param {Object} params Query parameters
     * @returns {Promise} Promise resolving to posts data and pagination info
     */
    async fetchPosts(params = {}) {
        try {
            const queryParams = new URLSearchParams({
                ...this.defaultParams,
                page: params.page || 1,
                per_page: params.posts_per_page || 9,
                orderby: params.orderby || 'date',
                order: params.order || 'DESC',
                search: params.search || '',
                categories: params.categories || ''
            });

            const response = await fetch(`${this.baseUrl}/posts?${queryParams.toString()}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return {
                posts: await response.json(),
                totalPosts: parseInt(response.headers.get('X-WP-Total')),
                totalPages: parseInt(response.headers.get('X-WP-TotalPages'))
            };
        } catch (error) {
            console.error('Error fetching posts:', error);
            throw error;
        }
    }

    /**
     * Fetch categories
     * @returns {Promise} Promise resolving to categories data
     */
    async fetchCategories() {
        try {
            const response = await fetch(`${this.baseUrl}/categories`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error fetching categories:', error);
            throw error;
        }
    }

    /**
     * Format post data for display
     * @param {Object} post Raw post data from API
     * @returns {Object} Formatted post data
     */
    formatPostData(post) {
        return {
            id: post.id,
            title: post.title.rendered,
            excerpt: post.excerpt.rendered,
            content: post.content.rendered,
            link: post.link,
            date: new Date(post.date).toLocaleDateString(),
            featuredMedia: post._embedded?.['wp:featuredmedia']?.[0]?.source_url || '',
            categories: post._embedded?.['wp:term']?.[0] || [],
            author: {
                name: post._embedded?.['author']?.[0]?.name || '',
                avatar: post._embedded?.['author']?.[0]?.avatar_urls?.[24] || ''
            }
        };
    }
} 
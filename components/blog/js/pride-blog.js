jQuery(document).ready(function($) {
    // Initialize blog components
    $('.pride-blog-container').each(function() {
        const container = $(this);
        const api = new PrideBlogAPI();
        new PrideBlogUI(container, api);
    });
}); 
<?php
/**
 * Blog Component Integration
 */

// Don't allow direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register and enqueue scripts and styles
 */
function pride_blog_enqueue_scripts() {
    // Enqueue React and ReactDOM from WordPress
    wp_enqueue_script('react');
    wp_enqueue_script('react-dom');

    // Enqueue our blog component
    wp_enqueue_script(
        'pride-blog-component',
        plugins_url('components/blog/dist/blog.js', dirname(__FILE__)),
        array('react', 'react-dom'),
        '1.0.0',
        true
    );

    // Pass necessary data to JavaScript
    wp_localize_script('pride-blog-component', 'prideBlogAjax', array(
        'nonce' => wp_create_nonce('wp_rest'),
        'restUrl' => rest_url()
    ));
}
add_action('wp_enqueue_scripts', 'pride_blog_enqueue_scripts');
add_action('admin_enqueue_scripts', 'pride_blog_enqueue_scripts');

/**
 * Register shortcode for the blog component
 */
function pride_blog_shortcode($atts) {
    // Parse attributes
    $attributes = shortcode_atts(array(
        'columns' => 3,
        'posts_per_page' => 6,
        'show_meta' => true,
        'show_excerpt' => true
    ), $atts);

    // Convert string values to proper types
    $attributes['columns'] = intval($attributes['columns']);
    $attributes['posts_per_page'] = intval($attributes['posts_per_page']);
    $attributes['show_meta'] = filter_var($attributes['show_meta'], FILTER_VALIDATE_BOOLEAN);
    $attributes['show_excerpt'] = filter_var($attributes['show_excerpt'], FILTER_VALIDATE_BOOLEAN);

    // Create a container for our React component
    $container_id = 'pride-blog-root-' . uniqid();
    
    ob_start();
    ?>
    <div id="<?php echo esc_attr($container_id); ?>"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('<?php echo esc_js($container_id); ?>');
            if (container && window.PrideBlog) {
                const root = ReactDOM.createRoot(container);
                root.render(React.createElement(PrideBlog.default, <?php echo json_encode($attributes); ?>));
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('pride_blog', 'pride_blog_shortcode'); 
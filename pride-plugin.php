<?php
/**
 * Plugin Name: Pride Plugin
 * Description: A beautiful and modern WordPress plugin that provides a responsive blog component.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: pride-plugin
 */

// Don't allow direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
if (!defined('PRIDE_PLUGIN_PATH')) {
    define('PRIDE_PLUGIN_PATH', plugin_dir_path(__FILE__));
}
if (!defined('PRIDE_PLUGIN_URL')) {
    define('PRIDE_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('PRIDE_PLUGIN_VERSION')) {
    define('PRIDE_PLUGIN_VERSION', '1.0.0');
}

// Include WordPress core
require_once(ABSPATH . 'wp-admin/includes/plugin.php');

// Include the blog component integration
require_once PRIDE_PLUGIN_PATH . 'components/blog/class-pride-blog.php';

// Include the blog admin component
require_once PRIDE_PLUGIN_PATH . 'admin/class-pride-blog-admin.php';

// Include the main plugin class
require_once PRIDE_PLUGIN_PATH . 'includes/class-pride-loader.php';

// Initialize the plugin
function pride_init() {
    // Register React scripts
    wp_register_script('react', 'https://unpkg.com/react@18/umd/react.production.min.js', array(), '18.0.0', true);
    wp_register_script('react-dom', 'https://unpkg.com/react-dom@18/umd/react-dom.production.min.js', array('react'), '18.0.0', true);

    return Pride_Loader::get_instance();
}
add_action('init', 'pride_init');

// Enqueue scripts and styles
function pride_enqueue_scripts() {
    wp_enqueue_style('pride-slider-style', PRIDE_PLUGIN_URL . 'assets/css/pride-slider.css', array(), PRIDE_PLUGIN_VERSION);
    wp_enqueue_script('pride-slider-script', PRIDE_PLUGIN_URL . 'assets/js/pride-slider.js', array('jquery'), PRIDE_PLUGIN_VERSION, true);
    
    // Localize script for AJAX
    wp_localize_script('pride-slider-script', 'prideAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('pride-slider-nonce')
    ));

    // Enqueue Tailwind CSS
    wp_enqueue_style('tailwindcss', 'https://cdn.tailwindcss.com', array(), '3.3.3');
    
    // Enqueue our custom Tailwind styles
    wp_enqueue_style('pride-blog-tailwind', plugin_dir_url(__FILE__) . 'components/blog/css/pride-blog-tailwind.css', array('tailwindcss'), '1.0.0');
}
add_action('wp_enqueue_scripts', 'pride_enqueue_scripts');

// Register shortcode for slider
function pride_slider_shortcode($atts) {
    // Default attributes
    $attributes = shortcode_atts(array(
        'images' => '',
        'speed' => '3000',
        'arrows' => 'true',
        'dots' => 'true'
    ), $atts);

    // Start output buffering
    ob_start();
    ?>
    <div class="pride-slider" 
         data-speed="<?php echo esc_attr($attributes['speed']); ?>"
         data-arrows="<?php echo esc_attr($attributes['arrows']); ?>"
         data-dots="<?php echo esc_attr($attributes['dots']); ?>">
        <?php
        if (!empty($attributes['images'])) {
            $image_ids = explode(',', $attributes['images']);
            foreach ($image_ids as $image_id) {
                $image_url = wp_get_attachment_image_url($image_id, 'full');
                if ($image_url) {
                    echo '<div class="pride-slide">';
                    echo '<img src="' . esc_url($image_url) . '" alt="Slider Image">';
                    echo '</div>';
                }
            }
        }
        ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('pride_slider', 'pride_slider_shortcode');

// Add admin menu
function pride_admin_menu() {
    add_menu_page(
        'Pride UI Components',
        'Pride UI',
        'manage_options',
        'pride-ui-components',
        'pride_admin_page',
        'dashicons-slides',
        20
    );
}
add_action('admin_menu', 'pride_admin_menu');

// Admin page callback
function pride_admin_page() {
    ?>
    <div class="wrap">
        <h1>Pride UI Components</h1>
        <div class="pride-admin-content">
            <h2>How to Use the Slider</h2>
            <p>Use the following shortcode to add a slider to your posts or pages:</p>
            <code>[pride_slider images="1,2,3" speed="3000" arrows="true" dots="true"]</code>
            
            <h3>Parameters:</h3>
            <ul>
                <li><strong>images</strong>: Comma-separated list of image IDs</li>
                <li><strong>speed</strong>: Transition speed in milliseconds (default: 3000)</li>
                <li><strong>arrows</strong>: Show navigation arrows (true/false)</li>
                <li><strong>dots</strong>: Show navigation dots (true/false)</li>
            </ul>
        </div>
    </div>
    <?php
} 
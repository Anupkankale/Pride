<?php
/**
 * Pride Blog Component
 * Handles blog post display and management functionality
 */

// Don't allow direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

class Pride_Blog {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('rest_api_init', array($this, 'register_rest_fields'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('pride_blog', array($this, 'render_blog_slider'));
        add_action('wp_ajax_get_latest_posts', array($this, 'get_latest_posts'));
        add_action('wp_ajax_nopriv_get_latest_posts', array($this, 'get_latest_posts'));
        add_action('wp_ajax_create_blog_post', array($this, 'create_blog_post'));
    }

    public function register_rest_fields() {
        // Register featured media
        register_rest_field('post', 'featured_media_url', array(
            'get_callback' => function($post) {
                if (has_post_thumbnail($post['id'])) {
                    $img_arr = wp_get_attachment_image_src(get_post_thumbnail_id($post['id']), 'full');
                    return $img_arr[0];
                }
                return null;
            }
        ));

        // Register author info
        register_rest_field('post', 'author_info', array(
            'get_callback' => function($post) {
                $author_id = $post['author'];
                return array(
                    'name' => get_the_author_meta('display_name', $author_id),
                    'avatar' => get_avatar_url($author_id, array('size' => 96))
                );
            }
        ));

        // Register categories
        register_rest_field('post', 'categories_info', array(
            'get_callback' => function($post) {
                $categories = get_the_category($post['id']);
                return array_map(function($cat) {
                    return array(
                        'id' => $cat->term_id,
                        'name' => $cat->name,
                        'slug' => $cat->slug
                    );
                }, $categories);
            }
        ));
    }

    public function enqueue_scripts() {
        // Enqueue API handler
        wp_enqueue_script(
            'pride-blog-api',
            plugin_dir_url(dirname(dirname(__FILE__))) . 'components/blog/js/pride-blog-api.js',
            array('jquery'),
            '1.0.0',
            true
        );

        // Enqueue UI manager
        wp_enqueue_script(
            'pride-blog-ui',
            plugin_dir_url(dirname(dirname(__FILE__))) . 'components/blog/js/pride-blog-ui.js',
            array('jquery', 'pride-blog-api'),
            '1.0.0',
            true
        );

        // Enqueue main script
        wp_enqueue_script(
            'pride-blog-js',
            plugin_dir_url(dirname(dirname(__FILE__))) . 'components/blog/js/pride-blog.js',
            array('jquery', 'pride-blog-api', 'pride-blog-ui'),
            '1.0.0',
            true
        );

        // Add REST API nonce
        wp_localize_script('pride-blog-api', 'prideRestApi', array(
            'root' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }

    public function render_blog_slider($atts) {
        $attributes = shortcode_atts(array(
            'posts_per_page' => 9,
            'category' => '',
            'order' => 'DESC',
            'orderby' => 'date',
            'layout' => 'grid'
        ), $atts);

        ob_start();
        ?>
        <div class="pride-blog-container" 
             data-posts-per-page="<?php echo esc_attr($attributes['posts_per_page']); ?>"
             data-layout="<?php echo esc_attr($attributes['layout']); ?>"
             data-category="<?php echo esc_attr($attributes['category']); ?>"
             data-order="<?php echo esc_attr($attributes['order']); ?>"
             data-orderby="<?php echo esc_attr($attributes['orderby']); ?>">
            
            <!-- Search and Sort Section -->
            <div class="flex justify-between items-center mb-8">
                <div class="pride-blog-search">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" placeholder="Search posts..." class="pride-blog-search-input">
                </div>
                <div class="pride-blog-sort">
                    <select>
                        <option value="date-DESC"><?php _e('Newest First', 'pride-plugin'); ?></option>
                        <option value="date-ASC"><?php _e('Oldest First', 'pride-plugin'); ?></option>
                        <option value="title-ASC"><?php _e('Title A-Z', 'pride-plugin'); ?></option>
                        <option value="title-DESC"><?php _e('Title Z-A', 'pride-plugin'); ?></option>
                    </select>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:flex lg:gap-8">
                <!-- Filters Sidebar -->
                <div class="lg:w-1/4">
                    <div class="pride-blog-filters">
                        <div class="pride-blog-filter-section">
                            <h3 class="pride-blog-filter-title"><?php _e('Categories', 'pride-plugin'); ?></h3>
                            <div class="pride-blog-filter-options">
                                <?php
                                $categories = get_categories();
                                foreach ($categories as $category) :
                                ?>
                                    <label class="pride-blog-filter-option">
                                        <input type="checkbox" class="pride-blog-filter-checkbox" value="<?php echo esc_attr($category->term_id); ?>">
                                        <span class="pride-blog-filter-label"><?php echo esc_html($category->name); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Posts Container -->
                <div class="lg:w-3/4">
                    <div class="<?php echo $attributes['layout'] === 'grid' ? 'pride-blog-grid' : 'pride-blog-list'; ?>">
                        <!-- Posts will be loaded here via JavaScript -->
                    </div>

                    <!-- Pagination -->
                    <div class="pride-blog-pagination">
                        <!-- Pagination will be loaded here via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function get_latest_posts() {
        check_ajax_referer('pride-blog-nonce', 'nonce');
        
        $posts = get_posts(array(
            'post_type' => 'post',
            'posts_per_page' => 5,
            'order' => 'DESC',
            'orderby' => 'date'
        ));

        $formatted_posts = array_map(function($post) {
            return array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'excerpt' => wp_trim_words($post->post_content, 20),
                'link' => get_permalink($post->ID),
                'thumbnail' => get_the_post_thumbnail_url($post->ID, 'large')
            );
        }, $posts);

        wp_send_json_success($formatted_posts);
    }

    public function create_blog_post() {
        check_ajax_referer('pride-blog-nonce', 'nonce');
        
        if (!current_user_can('publish_posts')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        $title = sanitize_text_field($_POST['title']);
        $content = wp_kses_post($_POST['content']);

        $post_data = array(
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => 'publish',
            'post_type' => 'post'
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            wp_send_json_error($post_id->get_error_message());
            return;
        }

        wp_send_json_success(array(
            'post_id' => $post_id,
            'permalink' => get_permalink($post_id)
        ));
    }
}

// Initialize the class
Pride_Blog::get_instance(); 
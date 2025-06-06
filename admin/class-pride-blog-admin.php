<?php
/**
 * Blog Component Admin Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Pride_Blog_Admin {
    private static $instance = null;
    private $option_name = 'pride_blog_settings';
    private $menu_slug = 'pride-blog-manager';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('admin_menu', array($this, 'add_menu_pages'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function add_menu_pages() {
        // Add main menu page if it doesn't exist
        global $submenu;
        if (!isset($submenu['pride-ui-components'])) {
            add_menu_page(
                __('Pride UI', 'pride-plugin'),
                __('Pride UI', 'pride-plugin'),
                'manage_options',
                'pride-ui-components',
                array($this, 'render_main_page'),
                'dashicons-layout',
                30
            );
        }

        // Add blog settings submenu
        add_submenu_page(
            'pride-ui-components',
            __('Blog Manager', 'pride-plugin'),
            __('Blog Manager', 'pride-plugin'),
            'manage_options',
            $this->menu_slug,
            array($this, 'render_settings_page')
        );
    }

    public function render_main_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div class="pride-admin-welcome">
                <h2><?php _e('Welcome to Pride UI Components', 'pride-plugin'); ?></h2>
                <p><?php _e('Manage your blog and other UI components from this central location.', 'pride-plugin'); ?></p>
                
                <div class="pride-admin-components">
                    <div class="pride-admin-component">
                        <h3><?php _e('Blog Manager', 'pride-plugin'); ?></h3>
                        <p><?php _e('Configure your blog display settings, manage posts, and customize the layout.', 'pride-plugin'); ?></p>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=' . $this->menu_slug)); ?>" class="button button-primary">
                            <?php _e('Manage Blog', 'pride-plugin'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function enqueue_admin_scripts($hook) {
        // Only load on our admin pages
        if (!in_array($hook, array(
            'toplevel_page_pride-ui-components',
            'pride-ui_page_' . $this->menu_slug
        ))) {
            return;
        }

        wp_enqueue_style('pride-blog-admin', PRIDE_PLUGIN_URL . 'admin/assets/css/pride-blog-admin.css', array(), PRIDE_PLUGIN_VERSION);
        wp_enqueue_script('pride-blog-admin', PRIDE_PLUGIN_URL . 'admin/assets/js/pride-blog-admin.js', array('jquery'), PRIDE_PLUGIN_VERSION, true);
        
        wp_localize_script('pride-blog-admin', 'prideBlogAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pride-blog-admin-nonce')
        ));
    }

    public function register_settings() {
        register_setting(
            'pride_blog_settings',
            $this->option_name,
            array($this, 'sanitize_settings')
        );

        add_settings_section(
            'pride_blog_general_section',
            __('General Settings', 'pride-plugin'),
            array($this, 'render_section_description'),
            $this->menu_slug
        );

        // Default Posts Per Page
        add_settings_field(
            'default_posts_per_page',
            __('Default Posts Per Page', 'pride-plugin'),
            array($this, 'render_number_field'),
            $this->menu_slug,
            'pride_blog_general_section',
            array(
                'label_for' => 'default_posts_per_page',
                'default' => 6,
                'min' => 1,
                'max' => 100
            )
        );

        // Default Layout
        add_settings_field(
            'default_layout',
            __('Default Layout', 'pride-plugin'),
            array($this, 'render_select_field'),
            $this->menu_slug,
            'pride_blog_general_section',
            array(
                'label_for' => 'default_layout',
                'options' => array(
                    'grid' => __('Grid', 'pride-plugin'),
                    'list' => __('List', 'pride-plugin')
                )
            )
        );

        // Default Columns
        add_settings_field(
            'default_columns',
            __('Default Grid Columns', 'pride-plugin'),
            array($this, 'render_select_field'),
            $this->menu_slug,
            'pride_blog_general_section',
            array(
                'label_for' => 'default_columns',
                'options' => array(
                    '1' => '1 Column',
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns'
                )
            )
        );

        // Show Excerpt
        add_settings_field(
            'show_excerpt',
            __('Show Excerpt', 'pride-plugin'),
            array($this, 'render_checkbox_field'),
            $this->menu_slug,
            'pride_blog_general_section',
            array(
                'label_for' => 'show_excerpt'
            )
        );

        // Excerpt Length
        add_settings_field(
            'excerpt_length',
            __('Excerpt Length (words)', 'pride-plugin'),
            array($this, 'render_number_field'),
            $this->menu_slug,
            'pride_blog_general_section',
            array(
                'label_for' => 'excerpt_length',
                'default' => 150,
                'min' => 10,
                'max' => 500
            )
        );

        // Show Meta Information
        add_settings_field(
            'show_meta',
            __('Show Meta Information', 'pride-plugin'),
            array($this, 'render_checkbox_field'),
            $this->menu_slug,
            'pride_blog_general_section',
            array(
                'label_for' => 'show_meta'
            )
        );
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $settings = get_option($this->option_name, array());
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <form action="options.php" method="post">
                <?php
                settings_fields('pride_blog_settings');
                do_settings_sections($this->menu_slug);
                submit_button(__('Save Settings', 'pride-plugin'));
                ?>
            </form>

            <hr>

            <h2><?php _e('Shortcode Usage', 'pride-plugin'); ?></h2>
            <div class="pride-blog-shortcode-usage">
                <p><?php _e('Use the following shortcode to display the blog posts:', 'pride-plugin'); ?></p>
                <code>[pride_blog]</code>

                <h3><?php _e('Available Parameters', 'pride-plugin'); ?></h3>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php _e('Parameter', 'pride-plugin'); ?></th>
                            <th><?php _e('Description', 'pride-plugin'); ?></th>
                            <th><?php _e('Default', 'pride-plugin'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>posts_per_page</code></td>
                            <td><?php _e('Number of posts to display', 'pride-plugin'); ?></td>
                            <td>6</td>
                        </tr>
                        <tr>
                            <td><code>layout</code></td>
                            <td><?php _e('Layout style (grid or list)', 'pride-plugin'); ?></td>
                            <td>grid</td>
                        </tr>
                        <tr>
                            <td><code>columns</code></td>
                            <td><?php _e('Number of columns in grid layout (1-4)', 'pride-plugin'); ?></td>
                            <td>3</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    public function render_section_description() {
        echo '<p>' . __('Configure how your blog posts are displayed on your website.', 'pride-plugin') . '</p>';
    }

    public function render_number_field($args) {
        $settings = get_option($this->option_name, array());
        $value = isset($settings[$args['label_for']]) ? $settings[$args['label_for']] : $args['default'];
        ?>
        <input type="number" 
               id="<?php echo esc_attr($args['label_for']); ?>"
               name="<?php echo esc_attr($this->option_name . '[' . $args['label_for'] . ']'); ?>"
               value="<?php echo esc_attr($value); ?>"
               min="<?php echo esc_attr($args['min']); ?>"
               max="<?php echo esc_attr($args['max']); ?>"
               class="regular-text">
        <?php
    }

    public function render_select_field($args) {
        $settings = get_option($this->option_name, array());
        $value = isset($settings[$args['label_for']]) ? $settings[$args['label_for']] : '';
        ?>
        <select id="<?php echo esc_attr($args['label_for']); ?>"
                name="<?php echo esc_attr($this->option_name . '[' . $args['label_for'] . ']'); ?>">
            <?php foreach ($args['options'] as $key => $label) : ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    public function render_checkbox_field($args) {
        $settings = get_option($this->option_name, array());
        $value = isset($settings[$args['label_for']]) ? $settings[$args['label_for']] : false;
        ?>
        <input type="checkbox"
               id="<?php echo esc_attr($args['label_for']); ?>"
               name="<?php echo esc_attr($this->option_name . '[' . $args['label_for'] . ']'); ?>"
               <?php checked($value, true); ?>>
        <?php
    }

    public function sanitize_settings($input) {
        $sanitized = array();

        if (isset($input['default_posts_per_page'])) {
            $sanitized['default_posts_per_page'] = absint($input['default_posts_per_page']);
        }

        if (isset($input['default_layout'])) {
            $sanitized['default_layout'] = sanitize_text_field($input['default_layout']);
        }

        if (isset($input['default_columns'])) {
            $sanitized['default_columns'] = absint($input['default_columns']);
        }

        if (isset($input['show_excerpt'])) {
            $sanitized['show_excerpt'] = (bool) $input['show_excerpt'];
        }

        if (isset($input['excerpt_length'])) {
            $sanitized['excerpt_length'] = absint($input['excerpt_length']);
        }

        if (isset($input['show_meta'])) {
            $sanitized['show_meta'] = (bool) $input['show_meta'];
        }

        return $sanitized;
    }
}

// Initialize the admin component
Pride_Blog_Admin::get_instance(); 
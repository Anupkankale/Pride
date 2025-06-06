<?php
/**
 * Main loader class for Pride UI Components
 */

if (!defined('ABSPATH')) {
    exit;
}

class Pride_Loader {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->define_constants();
        $this->load_dependencies();
        $this->init_hooks();
    }

    private function define_constants() {
        // Only define constants if they haven't been defined yet
        if (!defined('PRIDE_PLUGIN_PATH')) {
            define('PRIDE_PLUGIN_PATH', plugin_dir_path(dirname(__FILE__)));
        }
        if (!defined('PRIDE_PLUGIN_URL')) {
            define('PRIDE_PLUGIN_URL', plugin_dir_url(dirname(__FILE__)));
        }
        if (!defined('PRIDE_PLUGIN_VERSION')) {
            define('PRIDE_PLUGIN_VERSION', '1.0.0');
        }
    }

    private function load_dependencies() {
        // Load helper functions
        require_once PRIDE_PLUGIN_PATH . 'includes/pride-helper-functions.php';

        // Load components
        require_once PRIDE_PLUGIN_PATH . 'components/slider/class-pride-slider.php';
        require_once PRIDE_PLUGIN_PATH . 'components/blog/class-pride-blog.php';

        // Load admin
        if (is_admin()) {
            require_once PRIDE_PLUGIN_PATH . 'admin/class-pride-admin.php';
            require_once PRIDE_PLUGIN_PATH . 'admin/class-pride-blog-admin.php';
        }
    }

    private function init_hooks() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));
    }

    public function init() {
        // Initialize components
        Pride_Slider::get_instance();
        Pride_Blog::get_instance();

        // Initialize admin if we're in admin area
        if (is_admin()) {
            Pride_Admin::get_instance();
            Pride_Blog_Admin::get_instance();
        }
    }

    public function register_assets() {
        // Register common assets
        wp_register_style(
            'pride-common-style',
            PRIDE_PLUGIN_URL . 'assets/css/common.css',
            array(),
            PRIDE_PLUGIN_VERSION
        );

        wp_enqueue_style('pride-common-style');
    }
} 
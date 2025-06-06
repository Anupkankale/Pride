<?php
/**
 * Admin Class for Pride UI Components
 */

if (!defined('ABSPATH')) {
    exit;
}

class Pride_Admin {
    private static $instance = null;

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
        add_action('admin_menu', array($this, 'add_menu_page'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_pride-ui-components' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'pride-admin-style',
            PRIDE_PLUGIN_URL . 'admin/assets/css/admin.css',
            array(),
            PRIDE_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'pride-admin-script',
            PRIDE_PLUGIN_URL . 'admin/assets/js/admin.js',
            array('jquery'),
            PRIDE_PLUGIN_VERSION,
            true
        );
    }

    public function add_menu_page() {
        add_menu_page(
            __('Pride UI Components', 'pride-plugin'),
            __('Pride UI', 'pride-plugin'),
            'manage_options',
            'pride-ui-components',
            array($this, 'render_admin_page'),
            'dashicons-slides',
            20
        );
    }

    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Pride UI Components', 'pride-plugin'); ?></h1>
            
            <div class="pride-admin-tabs">
                <nav class="nav-tab-wrapper">
                    <a href="#slider" class="nav-tab nav-tab-active"><?php echo esc_html__('Slider', 'pride-plugin'); ?></a>
                    <a href="#settings" class="nav-tab"><?php echo esc_html__('Settings', 'pride-plugin'); ?></a>
                </nav>

                <div class="tab-content">
                    <div id="slider" class="tab-pane active">
                        <h2><?php echo esc_html__('How to Use the Slider', 'pride-plugin'); ?></h2>
                        <p><?php echo esc_html__('Use the following shortcode to add a slider to your posts or pages:', 'pride-plugin'); ?></p>
                        <code>[pride_slider images="1,2,3" speed="3000" arrows="true" dots="true"]</code>
                        
                        <h3><?php echo esc_html__('Parameters:', 'pride-plugin'); ?></h3>
                        <ul>
                            <li><strong>images</strong>: <?php echo esc_html__('Comma-separated list of image IDs', 'pride-plugin'); ?></li>
                            <li><strong>speed</strong>: <?php echo esc_html__('Transition speed in milliseconds (default: 3000)', 'pride-plugin'); ?></li>
                            <li><strong>arrows</strong>: <?php echo esc_html__('Show navigation arrows (true/false)', 'pride-plugin'); ?></li>
                            <li><strong>dots</strong>: <?php echo esc_html__('Show navigation dots (true/false)', 'pride-plugin'); ?></li>
                        </ul>
                    </div>

                    <div id="settings" class="tab-pane">
                        <h2><?php echo esc_html__('Global Settings', 'pride-plugin'); ?></h2>
                        <form method="post" action="options.php">
                            <?php
                            settings_fields('pride_ui_options');
                            do_settings_sections('pride_ui_options');
                            submit_button();
                            ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
} 
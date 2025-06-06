<?php
/**
 * Slider Component Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Pride_Slider {
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
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('pride_slider', array($this, 'render_slider'));
    }

    public function enqueue_assets() {
        wp_enqueue_style(
            'pride-slider-style',
            PRIDE_PLUGIN_URL . 'components/slider/assets/css/slider.css',
            array(),
            PRIDE_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'pride-slider-script',
            PRIDE_PLUGIN_URL . 'components/slider/assets/js/slider.js',
            array('jquery'),
            PRIDE_PLUGIN_VERSION,
            true
        );

        wp_localize_script('pride-slider-script', 'prideAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pride-slider-nonce')
        ));
    }

    public function render_slider($atts) {
        $attributes = shortcode_atts(array(
            'images' => '',
            'speed' => '3000',
            'arrows' => 'true',
            'dots' => 'true'
        ), $atts);

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
} 
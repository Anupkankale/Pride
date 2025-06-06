<?php
/**
 * Helper functions for Pride UI Components
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get plugin settings with defaults
 */
function pride_get_settings() {
    $defaults = array(
        'slider_default_speed' => 3000,
        'slider_default_arrows' => true,
        'slider_default_dots' => true,
    );

    $settings = get_option('pride_ui_settings', array());
    return wp_parse_args($settings, $defaults);
}

/**
 * Get component URL
 */
function pride_get_component_url($component) {
    return PRIDE_PLUGIN_URL . 'components/' . $component;
}

/**
 * Get component path
 */
function pride_get_component_path($component) {
    return PRIDE_PLUGIN_PATH . 'components/' . $component;
}

/**
 * Check if debug mode is enabled
 */
function pride_is_debug() {
    return defined('WP_DEBUG') && WP_DEBUG;
}

/**
 * Safe way to get asset URL with version
 */
function pride_get_asset_url($path) {
    $full_path = PRIDE_PLUGIN_PATH . $path;
    $version = pride_is_debug() ? filemtime($full_path) : PRIDE_PLUGIN_VERSION;
    
    return PRIDE_PLUGIN_URL . $path . '?ver=' . $version;
}

/**
 * Sanitize boolean value
 */
function pride_sanitize_boolean($value) {
    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
}

/**
 * Get image data
 */
function pride_get_image_data($attachment_id) {
    $attachment = get_post($attachment_id);
    
    if (!$attachment) {
        return false;
    }
    
    return array(
        'id' => $attachment->ID,
        'title' => get_the_title($attachment->ID),
        'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
        'caption' => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'url' => wp_get_attachment_url($attachment->ID),
        'sizes' => array(
            'full' => wp_get_attachment_image_src($attachment->ID, 'full'),
            'large' => wp_get_attachment_image_src($attachment->ID, 'large'),
            'medium' => wp_get_attachment_image_src($attachment->ID, 'medium'),
            'thumbnail' => wp_get_attachment_image_src($attachment->ID, 'thumbnail'),
        )
    );
} 
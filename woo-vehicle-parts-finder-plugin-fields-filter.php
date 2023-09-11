<?php
/*
Plugin Name: Woo Vehicle Parts Finder Plugin Fields Filter
Description: This plugin adds a custom product filter to WooCommerce Vehicle Parts Finder Plugin Fields.
Version: 1.0
Author: Sviatoslav Kachmar
*/

// Enqueue scripts and styles
// Enqueue scripts and styles
function custom_filter_enqueue_scripts() {
    // Enqueue jQuery and your custom JavaScript file
    wp_enqueue_script('jquery');
    wp_enqueue_script('custom-filter-script', plugins_url('/js/custom-filter.js', __FILE__), array('jquery'), '1.0', true);

    // Localize the AJAX URL to make it available in your JavaScript
    wp_localize_script('custom-filter-script', 'custom_filter_vars', array(
        'ajaxurl' => admin_url('admin-ajax.php'), // This sets the AJAX URL to the WordPress AJAX endpoint
    ));

    // Enqueue any necessary CSS styles
    wp_enqueue_style('custom-filter-style', plugins_url('/css/custom-filter.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'custom_filter_enqueue_scripts');

// Define the shortcode
function custom_filter_shortcode() {
    $atts = shortcode_atts(array(
        'model_order_by' => 'default', // Default sorting option for model
        'brand_order_by' => 'default', // Default sorting option for brand
    ), $atts);

    ob_start(); // Start output buffering

    // Include the HTML structure for the filter form, passing the attributes
    include(plugin_dir_path(__FILE__) . 'templates/filter-form.php');

    return ob_get_clean(); // End output buffering and return content
}
add_shortcode('Woo_Vehicle_Parts_Finder_Plugin_Fields_Filter', 'custom_filter_shortcode');// Include the HTML structure for the filter form, passing the attributes
    
// Include main filter functionality files
include(plugin_dir_path(__FILE__) . 'includes/custom_filter_ajax_handler.php');
include(plugin_dir_path(__FILE__) . 'includes/custom_filter_itself_ajax_handler.php');
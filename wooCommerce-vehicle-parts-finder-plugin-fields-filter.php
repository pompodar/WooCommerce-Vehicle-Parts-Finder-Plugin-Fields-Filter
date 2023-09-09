<?php
/*
Plugin Name: WooCommerce Vehicle Parts Finder Plugin Fields Filter
Description: This plugin adds a custom product filter to WooCommerce Vehicle Parts Finder Plugin Fields.
Version: 1.0
Author: Svjatoslav Kachmar
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
    ob_start(); // Start output buffering

    // Include the HTML structure for the filter form
    include(plugin_dir_path(__FILE__) . 'templates/filter-form.php');

    return ob_get_clean(); // End output buffering and return content
}
add_shortcode('WooCommerce_Vehicle_Parts_Finder_Plugin_Fields_Filter', 'custom_filter_shortcode');

// AJAX handler for filtering products
function custom_filter_ajax_handler() {
    // Check if the 'action' parameter is set and matches your defined action
    if (isset($_POST['action']) && $_POST['action'] == 'custom_filter_ajax_handler') {
        // Parse the form data sent via AJAX
        parse_str($_POST['formData'], $filter_data);

        // Construct the WP_Query arguments based on the filter criteria
        $args = array(
            'post_type' => 'product', // Adjust to your post type if necessary
            'posts_per_page' => -1,  // Display all matching products, you can adjust this as needed
            'meta_query' => array(),
        );

        // Add meta query conditions based on selected criteria
        if (!empty($filter_data['make'])) {
            $args['meta_query'][] = array(
                'key' => 'make',  // Replace with the actual custom field name for 'Make'
                'value' => sanitize_text_field($filter_data['make']),
                'compare' => '=',
            );
        }

        // Add similar meta query conditions for other filters (model, year, category, brand)

        // Run the WP_Query
        $products_query = new WP_Query($args);

        // Output the filtered products
        if ($products_query->have_posts()) {
            while ($products_query->have_posts()) {
                $products_query->the_post();
                // Display the product information here as needed
            }
            wp_reset_postdata(); // Reset the post data
        } else {
            echo 'No products found.'; // Output a message if no products match the criteria
        }

        // Always die at the end of your AJAX function
        die();
    }
}

// Hook the AJAX handler into WordPress
add_action('wp_ajax_custom_filter_ajax_handler', 'custom_filter_ajax_handler');
add_action('wp_ajax_nopriv_custom_filter_ajax_handler', 'custom_filter_ajax_handler');
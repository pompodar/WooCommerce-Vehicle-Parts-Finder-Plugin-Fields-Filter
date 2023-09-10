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
add_shortcode('Woo_Vehicle_Parts_Finder_Plugin_Fields_Filter', 'custom_filter_shortcode');

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

        // Initialize the tax_query array
        $args['tax_query'] = array();

        // Add tax query condition for "make" if it's selected
        if (!empty($filter_data['make'] && $filter_data['make'] != 'all')) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name',
                'terms' => sanitize_text_field($filter_data['make']),
                'operator' => 'IN',
            );
        }

        // Add tax query condition for "model" if it's selected
        if (!empty($filter_data['model'] && $filter_data['make'] != 'all')) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name', // You can use 'slug' or 'term_id' depending on your needs
                'terms' => sanitize_text_field($filter_data['model']),
                'operator' => 'IN',
            );
        }

        // Set the relation parameter to 'AND' to require both conditions to be met
        $args['tax_query']['relation'] = 'AND';


        // Add similar meta query conditions for other filters (model, year, category, brand)

        // Run the WP_Query
        $products_query = new WP_Query($args);

        // Output the filtered products
        if ($products_query->have_posts()) {
            while ($products_query->have_posts()) {
                $products_query->the_post();
                
                // Display the product information here
                $product_id = get_the_ID();
                $product_title = get_the_title();
                $product_price = get_post_meta($product_id, '_price', true);
                $product_link = get_permalink();
                
                if (!empty($product_price))  {    
                    echo '<div class="product">';
                    echo '<h2><a href="' . esc_url($product_link) . '">' . esc_html($product_title) . '</a></h2>';
                    echo '<p>Price: ' . wc_price($product_price) . '</p>';
                    // Add more product information as necessary
                    echo '</div>';
                }
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
add_action('wp_ajax_nopriv_custom_filter_ajax_handler', 'custom_filter_ajax_handler');// AJAX handler for filtering models based on the selected make

function custom_filter_itself_ajax_handler() {
    if (isset($_POST['action']) && $_POST['action'] == 'custom_filter_itself_ajax_handler') {

        // Get the selected "make"and ""model values
        $selected_make = isset($_POST['make']) ? sanitize_text_field($_POST['make']) : '';
        $selected_model = isset($_POST['model']) ? sanitize_text_field($_POST['model']) : '';

        // Get the filter value
        $filter = isset($_POST['filter']) ? sanitize_text_field($_POST['filter']) : '';

        // Specify the parent term name
        if ($filter == 'model') {
            $parent_term_name = $selected_make; 
        } else if ($filter == 'year') {
            $parent_term_name = $selected_model; 
        }
        
        // Specify the custom taxonomy
        $taxonomy = 'product_make'; 

        // Get the parent term object based on its name
        $parent_term = get_term_by('name', $parent_term_name, $taxonomy);

        if ($parent_term && !is_wp_error($parent_term)) {
            // Get the parent term ID
            $parent_term_id = $parent_term->term_id;

            // Get the direct child terms for the specified parent term
            $child_term_args = array(
                'taxonomy' => $taxonomy,
                'parent' => $parent_term_id,
            );

            $child_terms = get_terms($child_term_args);

            if (!empty($child_terms) && !is_wp_error($child_terms)) {
                foreach ($child_terms as $child_term) {
                    // Display child term information
                    $options[] = '<option value="' . esc_attr($child_term->name) . '">' . esc_html($child_term->name) . '</option>';
                }
            } else {
                echo 'No child terms found for the specified parent term (' . esc_html($parent_term_name) . ').';
            }
        } else {
            echo 'Parent term not found with the name: ' . esc_html($parent_term_name);
        }

        // Output the model options
        if (!empty($options)) {
            echo '<option value="" disabled>' . ucfirst($filter) . '</option>';
            echo '<option value="all">All ' . ucfirst($filter) . 's' . '</option>';
            echo implode('', $options);
        } else if ($parent_term_name == 'all') {
            echo '<option value="" disabled>' . ucfirst($filter) . '</option>';
            echo '<option value="all">All ' . ucfirst($filter) . 's' . '</option>';
        }

        // Always die at the end of your AJAX function
        die();
    }
}

// Hook the AJAX handler into WordPress
add_action('wp_ajax_custom_filter_itself_ajax_handler', 'custom_filter_itself_ajax_handler');
add_action('wp_ajax_nopriv_custom_filter_itself_ajax_handler', 'custom_itself_filter_ajax_handler');
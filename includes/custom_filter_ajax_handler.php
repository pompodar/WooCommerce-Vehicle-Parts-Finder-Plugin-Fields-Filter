<?php
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
        if (!empty($filter_data['model'] && $filter_data['model'] != 'all')) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name', // You can use 'slug' or 'term_id' depending on your needs
                'terms' => sanitize_text_field($filter_data['model']),
                'operator' => 'IN',
            );
        }

        // Add tax query condition for "year" if it's selected
        if (!empty($filter_data['year'] && $filter_data['year'] != 'all')) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name', // You can use 'slug' or 'term_id' depending on your needs
                'terms' => sanitize_text_field($filter_data['year']),
                'operator' => 'IN',
            );
        }

        // Add tax query condition for "year" if it's selected
        if (!empty($filter_data['year'] && $filter_data['year'] != 'all')) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field' => 'name', // You can use 'slug' or 'term_id' depending on your needs
                'terms' => sanitize_text_field($filter_data['category']),
                'operator' => 'IN',
            );
        }

        // Set the relation parameter to 'AND' to require both conditions to be met
        $args['tax_query']['relation'] = 'AND';

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
add_action('wp_ajax_nopriv_custom_filter_ajax_handler', 'custom_filter_ajax_handler');
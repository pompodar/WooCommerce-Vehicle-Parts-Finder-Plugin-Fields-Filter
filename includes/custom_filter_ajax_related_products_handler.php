<?php
// AJAX handler for filtering products
function custom_filter_ajax_related_products_handler() {
    // Check if the 'action' parameter is set and matches your defined action
    if (isset($_POST['action']) && $_POST['action'] == 'custom_filter_ajax_related_products_handler') {
        // Parse the form data sent via AJAX
        parse_str($_POST['formData'], $filter_data);

        // Construct the WP_Query arguments based on the filter criteria
        $args = array(
            'post_type' => 'product', // Adjust to your post type if necessary
            'posts_per_page' => -1,  // Display all matching products, you can adjust this as needed
            'meta_query' => array(),
            'hide_empty' => $hide_empty,
        );

        // Initialize the tax_query array
        $args['tax_query'] = array();

        // Add tax query conditions
        if (!empty($filter_data['brand'] && $filter_data['brand'] != 'all')) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name', // You can use 'slug' or 'term_id' depending on your needs
                'terms' => sanitize_text_field($filter_data['make']),
                'operator' => 'IN',
            );
            
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name',
                'terms' => sanitize_text_field($filter_data['model']),
                'operator' => 'IN',
            );
            
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name', // You can use 'slug' or 'term_id' depending on your needs
                'terms' => sanitize_text_field($filter_data['year']),
                'operator' => 'IN',
            );

            $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field' => 'name', // You can use 'slug' or 'term_id' depending on your needs
                'terms' => sanitize_text_field($filter_data['category']),
                'operator' => 'IN',
            );

            $args['tax_query'][] = array( 
                'taxonomy' => 'product_tag',
                'field' => 'name', // You can use 'slug' or 'term_id' depending on your needs
                'terms' => sanitize_text_field($filter_data['brand']),
                'operator' => 'NOT IN',
            );
        } if (!empty($filter_data['category'] && $filter_data['category'] != 'all')) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name', // You can use 'slug' or 'term_id' depending on your needs
                'terms' => sanitize_text_field($filter_data['make']),
                'operator' => 'IN',
            );
            
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name',
                'terms' => sanitize_text_field($filter_data['model']),
                'operator' => 'IN',
            );
            
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name', // You can use 'slug' or 'term_id' depending on your needs
                'terms' => sanitize_text_field($filter_data['year']),
                'operator' => 'IN',
            );

            $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field' => 'name', // You can use 'slug' or 'term_id' depending on your needs
                'terms' => sanitize_text_field($filter_data['category']),
                'operator' => 'NOT IN',
            );
        } else if (!empty($filter_data['year'] && $filter_data['year'] != 'all')) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name', // You can use 'slug' or 'term_id' depending on your needs
                'terms' => sanitize_text_field($filter_data['modal']),
                'operator' => 'IN',
            );
            
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name',
                'terms' => sanitize_text_field($filter_data['modal']),
                'operator' => 'IN',
            );
            
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name', // You can use 'slug' or 'term_id' depending on your needs
                'terms' => sanitize_text_field($filter_data['year']),
                'operator' => 'NOT IN',
            );
        } else if (!empty($filter_data['model'] && $filter_data['model'] != 'all')) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name', // You can use 'slug' or 'term_id' depending on your needs
                'terms' => sanitize_text_field($filter_data['make']),
                'operator' => 'IN',
            );
            
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name',
                'terms' => sanitize_text_field($filter_data['model']),
                'operator' => 'NOT IN',
            );
        } else if (!empty($filter_data['make'] && $filter_data['make'] != 'all')) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_make',
                'field' => 'name',
                'terms' => sanitize_text_field($filter_data['make']),
                'operator' => 'NOT IN',
            );
        } 
        
        // Set the relation parameter to 'AND' to require both conditions to be met
        $args['tax_query']['relation'] = 'AND';

        // Run the WP_Query
        $products_query = new WP_Query($args);

        // Output the filtered products
        if ($products_query->have_posts()) {
            $total_posts = $products_query->post_count;
            
            echo '<h2>Related Products</h2>';
            echo '<ul class="products columns-3">';
            echo '<p>' . $total_posts . ' found</p>';
            while ($products_query->have_posts()) {
                $products_query->the_post();

                // Display the product information here
                $product_id = get_the_ID();
                $product_title = get_the_title();
                $product_price = get_post_meta($product_id, '_price', true);
                $product_link = get_permalink();
                $product_image = get_the_post_thumbnail_url($product_id, 'woocommerce_thumbnail');

                if (!empty($product_price)) {
                    echo '<li class="product type-product status-publish instock has-post-thumbnail taxable shipping-taxable purchasable product-type-simple">';
                    echo '<a href="' . $product_link . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
                    echo '<img src="' . $product_image . '" alt="' . $product_title . '" decoding="async" loading="lazy">';
                    echo '<h2 class="woocommerce-loop-product__title">' . $product_title . '</h2>';
                    echo '<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">£</span>' . $product_price . '</bdi></span></span></a>';
                    echo '<a href="?add-to-cart=' . $product_id . '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . $product_id . '" data-product_sku="' . esc_attr($product_id) . '" aria-label="Add “' . $product_title . '” to your basket" rel="nofollow">Add to basket</a></li>';
                }
            }
            wp_reset_postdata(); // Reset the post data
            echo '</ul>';
        }

        $woo_vpf_taxonomy_metabox_excluded_products = WC_Admin_Settings::get_option( 'woo_vpf_taxonomy_metabox_excluded_products' );

        if (!empty($woo_vpf_taxonomy_metabox_excluded_products)) {
            // Convert the product IDs to an array of integers
            $product_ids = array_map('intval', $woo_vpf_taxonomy_metabox_excluded_products);

            // Query products based on the IDs
            $args = array(
                'post_type' => 'product',
                'post__in' => $product_ids,
                'posts_per_page' => -1,  // Display all matching products
            );

            $products_query = new WP_Query($args);

            if ($products_query->have_posts()) {
                $total_posts = $products_query->post_count;

                echo '<h2>Universal Products</h2>';
                echo '<ul class="products columns-3">';
                echo '<p>' . $total_posts . ' found</p>';
                while ($products_query->have_posts()) {
                    $products_query->the_post();

                    // Display the product information here
                    $product_id = get_the_ID();
                    $product_title = get_the_title();
                    $product_price = get_post_meta($product_id, '_price', true);
                    $product_link = get_permalink();
                    $product_image = get_the_post_thumbnail_url($product_id, 'woocommerce_thumbnail');

                    if (!empty($product_price)) {
                        echo '<li class="product type-product status-publish instock has-post-thumbnail taxable shipping-taxable purchasable product-type-simple">';
                        echo '<a href="' . $product_link . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
                        echo '<img src="' . $product_image . '" alt="' . $product_title . '" decoding="async" loading="lazy">';
                        echo '<h2 class="woocommerce-loop-product__title">' . $product_title . '</h2>';
                        echo '<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">£</span>' . $product_price . '</bdi></span></span></a>';
                        echo '<a href="?add-to-cart=' . $product_id . '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . $product_id . '" data-product_sku="' . esc_attr($product_id) . '" aria-label="Add “' . $product_title . '” to your basket" rel="nofollow">Add to basket</a></li>';
                    }
                }
                wp_reset_postdata(); // Reset the post data
                echo '</ul>';
            } else {
                echo 'No products found.'; // Output a message if no products match the criteria
            }
        }

        // Always die at the end of your AJAX function
        die();
    }
}

// Hook the AJAX handler into WordPress
add_action('wp_ajax_custom_filter_ajax_related_products_handler', 'custom_filter_ajax_related_products_handler');
add_action('wp_ajax_nopriv_custom_filter_ajax_related_products_handler', 'custom_filter_ajax_related_products_handler');
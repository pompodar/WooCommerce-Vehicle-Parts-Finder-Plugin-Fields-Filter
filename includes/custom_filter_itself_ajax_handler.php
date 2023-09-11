<?php
// AJAX handler for filtering models based on the selected make
function custom_filter_itself_ajax_handler() {
    if (isset($_POST['action']) && $_POST['action'] == 'custom_filter_itself_ajax_handler') {

        // Get the selected "make" and "model" values
        $selected_make = isset($_POST['make']) ? sanitize_text_field($_POST['make']) : '';
        $selected_model = (isset($_POST['model']) && $_POST['model'] !== 'all') ? sanitize_text_field($_POST['model']) : '';
        $selected_year = (isset($_POST['year']) && $_POST['year'] !== 'all') ? sanitize_text_field($_POST['year']) : '';
        $selected_category = (isset($_POST['category']) && $_POST['category'] !== 'all') ? sanitize_text_field($_POST['category']) : '';
        
        // Get the filter value
        $filter = isset($_POST['filter']) ? sanitize_text_field($_POST['filter']) : '';
        
        // Specify the parent term name
        if ($filter == 'model') {
            $parent_term_name = $selected_make; 
        } else if ($filter == 'year') {
            $parent_term_name = $selected_model; 
        }
            
        if ($filter == 'model' || $filter == 'year') {
            
            // Specify the custom taxonomy
            $taxonomy = 'product_make'; 

            // Initialize the tax_query array
            $tax_query = array();

            // Check if selected_make and selected_model exist
            if (!empty($selected_make)) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'name',
                    'terms'    => $selected_make,
                    'operator' => 'IN',
                );
            }

            if (!empty($selected_model)) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'name',
                    'terms'    => "$selected_model",
                    'operator' => 'IN',
                );
            }

            if (!empty($selected_year)) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'name',
                    'terms'    => "$selected_year",
                    'operator' => 'IN',
                );
            }
            
            // Check if there are any posts associated with the selected terms
            $args = array(
                'post_type' => 'product', // Adjust post type as needed
                'tax_query' => $tax_query, // Include the tax_query in the WP_Query
            );

            // Set the relation parameter to 'AND' to require both conditions to be met
            $args['tax_query']['relation'] = 'AND';

            $products_query = new WP_Query($args);

            if ($products_query->have_posts()) {

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
                    }
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
            }

            // Always die at the end of your AJAX function
            die();
        } else if ($filter == 'category' || $filter == 'brand') {
            // Specify the custom taxonomy
            $taxonomy = 'product_make'; 

            // Initialize the tax_query array
            $tax_query = array();

            // Check if selected_make and selected_model exist
            if (!empty($selected_make)) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'name',
                    'terms'    => $selected_make,
                    'operator' => 'IN',
                );
            }

            if (!empty($selected_model)) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'name',
                    'terms'    => "$selected_model",
                    'operator' => 'IN',
                );
            }

            if (!empty($selected_year)) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'name',
                    'terms'    => "$selected_year",
                    'operator' => 'IN',
                );
            }

            if (!empty($selected_category)) {
                $tax_query[] = array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'name',
                    'terms'    => $selected_category,
                    'operator' => 'IN',
                );
            }

            // Check if there are any posts associated with the selected terms
            $args = array(
                'post_type' => 'product', // Adjust post type as needed
                'tax_query' => $tax_query, // Include the tax_query in the WP_Query
            );

            // Set the relation parameter to 'AND' to require both conditions to be met
            $args['tax_query']['relation'] = 'AND';

            $products_query = new WP_Query($args);

            // Initialize an array to store unique category names
            $category_names = array();

            if ($products_query->have_posts()) {

                // Loop through the products
                while ($products_query->have_posts()) {
                    $products_query->the_post();

                    // Get the categories for the current product
                    $product_categories = wp_get_post_terms(get_the_ID(), 'product_cat'); 
                                        
                    // Get the brand of  for the current product
                    $product_brands = get_the_terms(get_the_ID(), 'product_tag');

                    // Loop through the categories or brands and add their names to the array 
                    if ($filter == 'category') {
                        foreach ($product_categories as $category) {
                            $category_names[] = $category->name;
                        }
                    } else if ($filter == 'brand') {
                        foreach ($product_brands as $brand) {
                            $category_names[] = $brand->name;
                        }
                    }

                }

                // Remove duplicates from the category names array
                $unique_category_names = array_unique($category_names);

                // Loop through the unique category names and add them to the options array
                foreach ($unique_category_names as $category_name) {
                    $options[] = '<option value="' . esc_attr($category_name) . '">' . esc_html($category_name) . '</option>';
                }

                // Output the model options (including categories)
                if (!empty($options)) {
                    echo '<option value="" disabled>' . ucfirst($filter) . '</option>';
                    
                    // Modify the "All" option text when the filter is "category"
                    $allOptionText = ($filter === 'category') ? 'All ' . str_replace('y' , '' , ucfirst($filter)) . 'ies' : 'All ' . ucfirst($filter) . 's';

                    echo '<option value="all">' . $allOptionText . '</option>';
                    echo implode('', $options);
                } else if ($parent_term_name == 'all') {
                    echo '<option value="" disabled>' . ucfirst($filter) . '</option>';
                    
                    // Modify the "All" option text when the filter is "category"
                    $allOptionText = ($filter === 'category') ? 'All ' . str_replace('y' , '' , ucfirst($filter)) . 'ies' : 'All ' . ucfirst($filter) . 's';

                    echo '<option value="all">' . $allOptionText . '</option>';
                }
            }
                        
            // Always die at the end of your AJAX function
            die();
        }
    }
}

// Hook the AJAX handler into WordPress
add_action('wp_ajax_custom_filter_itself_ajax_handler', 'custom_filter_itself_ajax_handler');
add_action('wp_ajax_nopriv_custom_filter_itself_ajax_handler', 'custom_itself_filter_ajax_handler');
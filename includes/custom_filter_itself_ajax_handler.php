<?php
// AJAX handler for filtering models based on the selected make
function custom_filter_itself_ajax_handler() {
    if (isset($_POST['action']) && $_POST['action'] == 'custom_filter_itself_ajax_handler') {

        // Get the selected "make" and "model" values
        $selected_make = isset($_POST['make']) ? sanitize_text_field($_POST['make']) : '';
        $selected_model = (isset($_POST['model']) && $_POST['model'] !== 'all') ? sanitize_text_field($_POST['model']) : '';
        $selected_year = (isset($_POST['year']) && $_POST['year'] !== 'all') ? sanitize_text_field($_POST['year']) : '';
        $selected_category = (isset($_POST['category']) && $_POST['category'] !== 'all') ? sanitize_text_field($_POST['category']) : '';
        
        $model_order_by = (isset($_POST['model_order_by'])) ? sanitize_text_field($_POST['model_order_by']) : '';
        $category_order_by = (isset($_POST['category_order_by'])) ? sanitize_text_field($_POST['category_order_by']) : '';
        $brand_order_by = (isset($_POST['brand_order_by'])) ? sanitize_text_field($_POST['brand_order_by']) : '';

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

                    // Initialize the options array
                    $options = array();

                    // Check Model Order By value
                    if ($model_order_by == 'custom' && $filter == 'model') {
                        $option_name = 'wvpfpff_plugin_' . $parent_term->name . 'models_item_order';
                        
                        $options_array = get_option($option_name, array());
                        
                        foreach ($options_array as $option) {
                            // Display child term information
                            $options[] = '<option value="' . esc_attr($option) . '">' . esc_html($option) . '</option>';
                         }
                    } else {
                        if (!empty($child_terms) && !is_wp_error($child_terms)) {
                            foreach ($child_terms as $child_term) {
                                // Replace All years
                                if ($filter == "year" && $child_term->name == "All Years") {
                                    // Display child term information
                                    $options[] = '<option value="All Years">all possible</option>';
                                } else {
                                    // Display child term information
                                    $options[] = '<option value="' . esc_attr($child_term->name) . '">' . esc_html($child_term->name) . '</option>';
                                }                            
                            }
                        }
                    }
                }
                
                // Reverse years
                if ($filter == 'year') {
                    $reversed_options = array_reverse($options);

                    // Output the year options
                    if (!empty($reversed_options)) {
                        echo '<option class="wvpfpff-disabled" value="all" disabled>' . ucfirst($filter) . '</option>';
                        echo '<option value="all">All ' . ucfirst($filter) . 's' . '</option>';
                        echo implode('', $reversed_options);
                    } else if ($parent_term_name == 'all') {
                        echo '<option class="wvpfpff-disabled" value="all" disabled>' . ucfirst($filter) . '</option>';
                        echo '<option value="all">All ' . ucfirst($filter) . 's' . '</option>';
                    } else {
                        echo '<option class="wvpfpff-disabled" value="all" disabled>' . 'No ' . ucfirst($filter) . 's' . '</option>';
                    }
                } else {
                    // Output the model options
                    if (!empty($options)) {
                        echo '<option class="wvpfpff-disabled" value="all" disabled>' . ucfirst($filter) . '</option>';
                        echo '<option value="all">All ' . ucfirst($filter) . 's' . '</option>';
                        echo implode('', $options);
                    } else if ($parent_term_name == 'all') {
                        echo '<option class="wvpfpff-disabled" value="all" disabled>' . ucfirst($filter) . '</option>';
                        echo '<option value="all">All ' . ucfirst($filter) . 's' . '</option>';
                    } else {
                        echo '<option class="wvpfpff-disabled" value="all" disabled>' . 'No ' . ucfirst($filter) . 's' . '</option>';
                    }   
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

                // Initialize an options array
                $options = array();
                    
                // Check Category Order By and Brand Order by values
                if ($category_order_by == 'custom' && $filter == 'category') {
                    $custom_ordered_cats = get_option('wvpfpff_plugin_category_item_order', array());
                    
                    // Define a a sorted array
                    $sorted_options = array();

                    // Sort the array by custom order
                    for($i = 0; $i < count($custom_ordered_cats); $i++) {   
                        if(in_array($custom_ordered_cats[$i], $unique_category_names)) {
                            $sorted_options[] = $custom_ordered_cats[$i];
                        }
                    }    
                    
                    // Loop through the unique category names and add them to the options array
                    foreach ($sorted_options as $category_name) {
                        $options[] = '<option value="' . esc_attr($category_name) . '">' . esc_html($category_name) . '</option>';
                    }
                } else if ($brand_order_by == 'custom' && $filter == 'brand') {
                    $custom_ordered_brands = get_option('wvpfpff_plugin_tag_item_order', array());
                    
                    $sorted_options = array();

                    // Sort the array by custom order
                    for($i = 0; $i < count($custom_ordered_brands); $i++) {   
                        if(in_array(get_term($custom_ordered_brands[$i], 'product_tag')->name, $unique_category_names)) {
                            $sorted_options[] = get_term($custom_ordered_brands[$i], 'product_tag')->name;
                        }
                    } 

                    // Loop through the unique tags names and add them to the options array
                    foreach ($sorted_options as $tag_name) {
                        $options[] = '<option value="' . esc_attr($tag_name) . '">' . esc_html($tag_name) . '</option>';
                    }
                } else {
                    // Loop through the unique category names and add them to the options array
                    foreach ($unique_category_names as $category_name) {
                        $options[] = '<option value="' . esc_attr($category_name) . '">' . esc_html($category_name) . '</option>';
                    }   
                }
                                // Modify the "All" option text when the filter is "category"
                $allOptionText = ($filter === 'category') ? 'All ' . str_replace('y' , '' , ucfirst($filter)) . 'ies' : 'All ' . ucfirst($filter) . 's';

                // Output the model options (including categories)
                if (!empty($options)) {
                    echo '<option class="wvpfpff-disabled" value="all" disabled>' . ucfirst($filter) . '</option>';
                    echo '<option value="all">' . $allOptionText . '</option>';
                    echo implode('', $options);
                } else if ($parent_term_name == 'all') {
                    echo '<option class="wvpfpff-disabled" value="all" disabled>' . ucfirst($filter) . '</option>';
                    echo '<option value="all">' . $allOptionText . '</option>';
                } else {
                    echo '<option value="all">' . 'No ' .  str_replace('All', '', $allOptionText) . '</option>';
                }
            }

            // Always die at the end of your AJAX function
            die();
        }
    }
}

// Hook the AJAX handler into WordPress
add_action('wp_ajax_custom_filter_itself_ajax_handler', 'custom_filter_itself_ajax_handler');
add_action('wp_ajax_nopriv_custom_filter_itself_ajax_handler', 'custom_filter_itself_ajax_handler');
?>
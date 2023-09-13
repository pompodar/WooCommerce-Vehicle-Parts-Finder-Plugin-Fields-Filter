<?php
// Enqueue necessary scripts and styles for the admin page
function wvpfpff_plugin_enqueue_assets($hook) {
    if ($hook === 'toplevel_page_wvpfpff-plugin-rearrange') {
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('wvpfpff-plugin-admin', plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery', 'jquery-ui-sortable'), '1.0', true);
        wp_enqueue_style('wvpfpff-plugin-admin-style', plugin_dir_url(__FILE__) . 'css/admin.css');
        wp_localize_script('wvpfpff-plugin-admin', 'wvpfpff_filter_vars', array(
            'ajaxurl' => admin_url('admin-ajax.php'), // This sets the AJAX URL to the WordPress AJAX endpoint
        ));
    }
}
add_action('admin_enqueue_scripts', 'wvpfpff_plugin_enqueue_assets');

// Render the admin page
function wvpfpff_plugin_render_admin_page() {
    // Retrieve the stored order from the options
    $current_order = get_option('wvpfpff_plugin_item_order', array());

    // Get all parent terms of the 'product_make' taxonomy
    $parent_terms = get_terms(array(
        'taxonomy' => 'product_make',
        'parent' => 0, // Get only top-level parent terms
        'hide_empty' => true, // Exclude empty terms in the result
    ));

    $parent_term_names = array();

    if (!empty($parent_terms) && !is_wp_error($parent_terms)) {
        foreach ($parent_terms as $parent_term) {
            $parent_term_name = preg_replace('/[^a-zA-Z0-9]+/', ' ', str_replace('andamp', 'and', str_replace('&', 'and', $parent_term->name)));;
            $parent_term_names[] = $parent_term_name;
        }
    }

    // Retrieve possibly updated data
    $new_order = $parent_term_names;

    // Check for additions (items in new order but not in the current order)
    $added_items = array_diff($new_order, $current_order);

    // Check for removals (items in current order but not in the new order)
    $removed_items = array_diff($current_order, $new_order);

    // Update changes when the user submits the form
    if (isset($_POST['submit'])) {
        update_option('wvpfpff_plugin_item_order', $new_order);
    }

    // Display the "plus" button and the sortable list
    echo '<div class="wrapper">';
    echo '<h1>Rearrange Orders</h1>';
    echo '<h2 style="display: none">Makes</h2>';
    echo '<button style="display: none" id="toggle-form">Show Makes</button>'; // Add a "plus" button
    echo '<form style="display: none" method="post" id="sortable-form" style="display:none;">'; // Initially hide the form
    
    // Display notifications
    if (!(count( $added_items ) == count( $removed_items ) && !array_diff( $added_items, $removed_items ))) {
        if (!empty($added_items)) {
            echo '<div class="update bad-news">';
            echo '<p>Added items for makes: ' . implode(', ', $added_items) . '</p>';
            echo '</div>';
        } else {
            echo '<div class="update good-news">';
            echo '<p>No added items for makes</p>';
            echo '</div>';
        }
        if (!empty($removed_items)) {
            echo '<div class="update bad-news">';
            echo '<p>Removed items for makes: ' . implode(', ', $removed_items) . '</p>';
            echo '</div>';
        } else {
            echo '<div class="update good-news">';
            echo '<p>No removed items for makes</p>';
            echo '</div>';
        }    
    } else {
        echo '<div class="update good-news">';
        echo '<p>No added items for makes</p>';
        echo '</div>';
        echo '<div class="update good-news">';
        echo '<p>No removed items for makes</p>';
        echo '</div>';
    }
    
    if (!empty($added_items) || !empty($removed_items)) {
        echo '<input type="submit" name="submit" value="Save Changes">';
        echo '<p class="warning">Please update the page after saving!</p>';
    }
    echo '<div class="wvpfppff-admin-spinner warning" id="my-spinner" style="display: none;">Loading...</div>';
    echo '<ul id="sortable-list">';

    if (!empty($current_order)) {
        foreach ($current_order as $index => $item) {
            // Use the $index as the ID for the list item
            echo '<li class="sortable-item" id="' . esc_attr($index) . '">' . esc_html($item) . '</li>';
        }
    } else {
        foreach ($new_order as $index => $item) {
            // Use the $index as the ID for the list item
            echo '<li class="sortable-item" id="' . esc_attr($index) . '">' . esc_html($item) . '</li>';
        }
    }

    echo '</ul>';
    echo '</form>';
    echo '</div>';
    echo '<hr/>';

    // Models
    if (!empty($parent_terms) && !is_wp_error($parent_terms)) {
        foreach ($parent_terms as $parent_term) {
            $parent_term_names[] = $parent_term->name;
            
            // Get direct children of this parent term
            $children = get_terms(array(
                'taxonomy' => 'product_make',
                'parent' => $parent_term->term_id,
                'hide_empty' => true, // Exclude empty terms in the result
            ));

            $option_name = 'wvpfpff_plugin_' . $parent_term->name . 'models_item_order';

            // Retrieve the stored order from the options
            $current_order = get_option($option_name, array());
            
            $child_term_names = array();
            
            if (!empty($children) && !is_wp_error($children)) {
                foreach ($children as $child_term) {
                    $children_name = preg_replace('/[^a-zA-Z0-9]+/', ' ', str_replace('andamp', 'and', str_replace('&', 'and', $child_term->name)));
                    $child_term_names[] = $children_name;
                }
            }

            // Retrieve possibly updated data for the combined list
            $new_order = $child_term_names;
            
            // Check if there are additions (items in new order but not in the current order)
            $added_items = array_diff($child_term_names, $current_order);

            // Check if there are removals (items in current order but not in the new order)
            $removed_items = array_diff($current_order, $child_term_names);

            // Check if the user has submitted the form for this parent term
            if (isset($_POST['submit_' . $parent_term->term_id])) {
                update_option($option_name, $new_order);
            }

            // Display the sortable list with the current order for this parent term
            echo '<div class="wrapper">';
            echo '<h2>Models</h2>';
            echo '<h4>' . esc_html($parent_term->name) . '</h4>';
            echo '<button class="toggle-form-models" id="toggle-form_' . esc_attr($parent_term->term_id) . '">Show Models</button>'; // Add a "plus" button
            echo '<form class="sortable-form-models" method="post" id="sortable-form_' . esc_attr($parent_term->term_id) . '" style="display:none;">'; // Initially hide the form
            echo '<input type="hidden" name="parent_term_name" value="' . esc_attr($parent_term->name) . '">';
            
            // Display notifications for child terms
        if (!(count( $added_items ) == count( $removed_items ) && !array_diff( $added_items, $removed_items ))) {
            if (!empty($added_items)) {
                echo '<div class="update bad-news">';
                echo '<p>Added items for models: ' . implode(', ', $added_items) . '</p>';
                echo '</div>';
            } else {
                echo '<div class="update good-news">';
                echo '<p>No added items for models</p>';
                echo '</div>';
            }
            if (!empty($removed_items)) {
                echo '<div class="update bad-news">';
                echo '<p>Removed items for models: ' . implode(', ', $removed_items) . '</p>';
                echo '</div>';
            } else {
                echo '<div class="update good-news">';
                echo '<p>No removed items for models</p>';
                echo '</div>';
            }    
        } else {
            echo '<div class="update good-news">';
            echo '<p>No added items for models</p>';
            echo '</div>';
            echo '<div class="update good-news">';
            echo '<p>No removed items for models</p>';
            echo '</div>';
        }

        if (!empty($added_items) || !empty($removed_items)) {
            echo '<input type="submit" name="submit_' . esc_attr($parent_term->term_id) . '" value="Save Changes">';
            echo '<p class="warning">Please update the page after saving!</p>';
        }
        echo '<div class="wvpfppff-admin-spinner-models warning" id="wvpfppff-admin-spinner_' . esc_attr($parent_term->term_id) . '" style="display: none;">Loading...</div>';
        echo '<ul class="sortable-list-models" id="sortable-list_' . esc_attr($parent_term->term_id) . '">';

        if (!empty($current_order)) {
            foreach ($current_order as $index => $item) {
                // Use the $index as the ID for the list item
                echo '<li class="sortable-item" id="' . esc_attr($index) . '">' . esc_html($item) . '</li>';
            }
        } else {
            foreach ($new_order as $index => $item) {
                // Use the $index as the ID for the list item
                echo '<li class="sortable-item" id="' . esc_attr($index) . '">' . esc_html($item) . '</li>';
            }
        }

        echo '</ul>';
        echo '</form>';
        echo '</div>';
        echo '<hr/>';
    }

    // Categories
    $products_cats = array();
    
    $terms = get_terms(array(
        'taxonomy' => 'product_cat', // Taxonomy name for product categories
        'hide_empty' => true,       // Exclude empty categories
    ));

    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $category_name = preg_replace('/[^a-zA-Z0-9]+/', ' ', str_replace('andamp', 'and', str_replace('&', 'and', $term->name)));
            $products_cats[] = $category_name;
        }
    } else {
        echo 'No product categories found.';
    }
    
    $option_name = 'wvpfpff_plugin_category_item_order';

    // Retrieve the stored order from the options
    $current_order = get_option($option_name, array());
    
    // Retrieve possibly updated data for the combined list
    $new_order = $products_cats;

    // Check if there are additions (items in new order but not in the current order)
    $added_items = array_diff($new_order, $current_order);

    // Check if there are removals (items in current order but not in the new order)
    $removed_items = array_diff($current_order, $new_order);

    // Check if the user has submitted the form
    if (isset($_POST['submit_categories'])) {
        update_option($option_name, $new_order);
    }

    // Display the sortable list with the current order 
    echo '<div class="wrapper">';
    echo '<h2>Categories</h2>';
    echo '<button class="toggle-form-categories">Show Categories</button>'; // Add a "plus" button
    echo '<form class="sortable-form-categories" method="post" style="display:none;">'; // Initially hide the form
    
    // Display notifications
    if (!(count( $added_items ) == count( $removed_items ) && !array_diff( $added_items, $removed_items ))) {
        if (!empty($added_items)) {
            echo '<div class="update bad-news">';
            echo '<p>Added items for categories: ' . implode(', ', $added_items) . '</p>';
            echo '</div>';
        } else {
            echo '<div class="update good-news">';
            echo '<p>No added items for categories</p>';
            echo '</div>';
        }
        if (!empty($removed_items)) {
            echo '<div class="update bad-news">';
            echo '<p>Removed items for categories: ' . implode(', ', $removed_items) . '</p>';
            echo '</div>';
        } else {
            echo '<div class="update good-news">';
            echo '<p>No removed items for categories</p>';
            echo '</div>';
        }    
    } else {
        echo '<div class="update good-news">';
        echo '<p>No added items for categories</p>';
        echo '</div>';
        echo '<div class="update good-news">';
        echo '<p>No removed items for categories</p>';
        echo '</div>';
    }
    
    if (!empty($added_items) || !empty($removed_items)) {
        echo '<input type="submit" name="submit_categories" value="Save Changes">';
        echo '<p class="warning">Please update the page after saving!</p>';
    }
    echo '<div class="wvpfppff-admin-spinner-categories warning" style="display: none;">Loading...</div>';
    echo '<ul id="sortable-list-categories" class="sortable-list-categories">';

    if (!empty($current_order)) {
        foreach ($current_order as $index => $item) {
            // Use the $index as the ID for the list item
            echo '<li class="sortable-item" id="' . esc_attr($index) . '">' . esc_html($item) . '</li>';
        }
    } else {
        foreach ($new_order as $index => $item) {
            // Use the $index as the ID for the list item
            echo '<li class="sortable-item" id="' . esc_attr($index) . '">' . esc_html($item) . '</li>';
        }
    }

    echo '</ul>';
    echo '</form>';
    echo '</div>';
    echo '<href/>';

    // Tags
    $products_tags = array();
    
    $terms = get_terms(array(
        'taxonomy' => 'product_tag', // Taxonomy name for product tags
        'hide_empty' => true,       // Exclude empty tags
    ));

    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $tag_name = preg_replace('/[^a-zA-Z0-9]+/', ' ', str_replace('andamp', 'and', str_replace('&', 'and', $term->name)));
            $products_tags[] = $tag_name;
        }
    } else {
        echo 'No product tags found.';
    }
    
    $option_name = 'wvpfpff_plugin_tag_item_order';

    // Retrieve the stored order from the options
    $current_order = get_option($option_name, array());
    
    // Retrieve possibly updated data for the combined list
    $new_order = $products_tags;

    // Check if there are additions (items in new order but not in the current order)
    $added_items = array_diff($new_order, $current_order);

    // Check if there are removals (items in current order but not in the new order)
    $removed_items = array_diff($current_order, $new_order);

    // Check if the user has submitted the form for this parent term
    if (isset($_POST['submit_tags'])) {
        update_option($option_name, $new_order);
    }

    // Display the sortable list with the current order 
    echo '<div class="wrapper">';
    echo '<h2>Brands</h2>';
    echo '<button class="toggle-form-tags">Show Brands</button>'; // Add a "plus" button
    echo '<form class="sortable-form-tags" method="post" style="display:none;">'; // Initially hide the form
    
    // Display notifications
    if (!(count( $added_items ) == count( $removed_items ) && !array_diff( $added_items, $removed_items ))) {
        if (!empty($added_items)) {
            echo '<div class="update bad-news">';
            echo '<p>Added items for brands: ' . implode(', ', $added_items) . '</p>';
            echo '</div>';
        } else {
            echo '<div class="update good-news">';
            echo '<p>No added items for brands</p>';
            echo '</div>';
        }
        if (!empty($removed_items)) {
            echo '<div class="update bad-news">';
            echo '<p>Removed items for brands: ' . implode(', ', $removed_items) . '</p>';
            echo '</div>';
        } else {
            echo '<div class="update good-news">';
            echo '<p>No removed items for brands</p>';
            echo '</div>';
        }    
    } else {
        echo '<div class="update good-news">';
        echo '<p>No added items for brands</p>';
        echo '</div>';
        echo '<div class="update good-news">';
        echo '<p>No removed items for brands</p>';
        echo '</div>';
    }
    
    if (!empty($added_items) || !empty($removed_items)) {
        echo '<input type="submit" name="submit_tags" value="Save Changes">';
        echo '<p class="warning">Please update the page after saving!</p>';
    }
    echo '<div class="wvpfppff-admin-spinner-tags warning" style="display: none;">Loading...</div>';
    echo '<ul id="sortable-list-tags" class="sortable-list-categories">';

    if (!empty($current_order)) {
        foreach ($current_order as $index => $item) {
            // Use the $index as the ID for the list item
            echo '<li class="sortable-item" id="' . esc_attr($index) . '">' . esc_html($item) . '</li>';
        }
    } else {
        foreach ($new_order as $index => $item) {
            // Use the $index as the ID for the list item
            echo '<li class="sortable-item" id="' . esc_attr($index) . '">' . esc_html($item) . '</li>';
        }
    }

    echo '</ul>';
    echo '</form>';
    echo '</div>';
    echo '<href/>';
    }
}
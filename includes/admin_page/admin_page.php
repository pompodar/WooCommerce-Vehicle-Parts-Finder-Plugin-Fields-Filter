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
        'hide_empty' => false, // Include empty terms in the result
    ));

    $parent_term_names = array();

    if (!empty($parent_terms) && !is_wp_error($parent_terms)) {
        foreach ($parent_terms as $parent_term) {
            $parent_term_names[] = $parent_term->name;
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
    echo '<h2>Makes</h2>';
    echo '<button id="toggle-form">Show Makes</button>'; // Add a "plus" button
    echo '<form method="post" id="sortable-form" style="display:none;">'; // Initially hide the form
    
    // Display notifications
    if (!empty($added_items)) {
        echo '<div class="update bad-news">
            <p>Added items: ' . implode(', ', $added_items) . '</p>
        </div>';
    } else {
        echo '<div class="update good-news">
            <p>No added items</p>
        </div>';
    }
    if (!empty($removed_items)) {
        echo '<div class="update bad-news">
            <p>Removed items: ' . implode(', ', $removed_items) . '</p>
        </div>';
    } else {
        echo '<div class="update good-news">
            <p>No removed items</p>
        </div>';
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
                'hide_empty' => false, // Include empty terms in the result
            ));

            $option_name = 'wvpfpff_plugin_' . $parent_term->name . 'models_item_order';

            // Retrieve the stored order from the options
            $current_order = get_option($option_name, array());
            
            $child_term_names = array();
            
            if (!empty($children) && !is_wp_error($children)) {
                foreach ($children as $child_term) {
                    $child_term_names[] = $child_term->name;
                }
            }
            
            // Check if there are additions (items in new order but not in the current order)
            $added_items = array_diff($child_term_names, $current_order);

            // Check if there are removals (items in current order but not in the new order)
            $removed_items = array_diff($current_order, $child_term_names);
            
            // Retrieve possibly updated data for the combined list
            $new_order = $child_term_names;

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
            if (!empty($added_items)) {
                echo '<div class="update bad-news">';
                echo '<p>Added items for ' . esc_html($parent_term->name) . ': ' . implode(', ', $added_items) . '</p>';
                echo '</div>';
            } else {
                echo '<div class="update good-news">';
                echo '<p>No added items for ' . esc_html($parent_term->name) . '</p>';
                echo '</div>';
            }
            if (!empty($removed_items)) {
                echo '<div class="update bad-news">';
                echo '<p>Removed items for ' . esc_html($parent_term->name) . ': ' . implode(', ', $removed_items) . '</p>';
                echo '</div>';
            } else {
                echo '<div class="update good-news">';
                echo '<p>No removed items for ' . esc_html($parent_term->name) . '</p>';
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
        }
    }
}
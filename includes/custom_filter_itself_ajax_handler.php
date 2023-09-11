<?php
// AJAX handler for filtering models based on the selected make
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
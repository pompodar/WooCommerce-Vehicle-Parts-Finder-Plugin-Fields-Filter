<?php
// AJAX handler for updating the item order for makes
function wvpfpff_plugin_update_item_order() {
    if (isset($_POST['new_order'])) {
        $new_order = $_POST['new_order'];
        update_option('wvpfpff_plugin_item_order', $new_order);
        
        // Return a success response
        wp_send_json_success('Item order updated successfully.');
    }
    
    // If the request didn't contain the 'new_order' parameter, return an error
    wp_send_json_error('Invalid request.');
}

add_action('wp_ajax_wvpfpff_plugin_update_item_order', 'wvpfpff_plugin_update_item_order');

// Models
function wvpfpff_plugin_update_model_item_order() {
    if (isset($_POST['new_order'])) {
        $make = $_POST['make'];
        $new_order = $_POST['new_order'];
        $option_name = 'wvpfpff_plugin_' . $make . 'models_item_order';
        update_option($option_name, $new_order);
        
        // Return a success response
        wp_send_json_success('Item order updated successfully.');
    }
    
    // If the request didn't contain the 'new_order' parameter, return an error
    wp_send_json_error('Invalid request.');
}

add_action('wp_ajax_wvpfpff_plugin_update_model_item_order', 'wvpfpff_plugin_update_model_item_order');
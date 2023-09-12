<?php
// AJAX handler for updating the item order
function wvpfpff_plugin_update_item_order() {
    if (isset($_POST['new_order'])) {
        $new_order = $_POST['new_order'];
        update_option('wvpfpff_plugin_item_order', $new_order);
        die();
    }
}
add_action('wp_ajax_my_plugin_update_item_order', 'wvpfpff_plugin_update_item_order');
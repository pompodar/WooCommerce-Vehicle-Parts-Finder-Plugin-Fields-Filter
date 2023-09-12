<?php
// Enqueue necessary scripts and styles for the admin page
function vpfpff_plugin_enqueue_assets($hook) {
    if ($hook !== 'toplevel_page_vpfpff-plugin-rearrange') {
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('vpfpff-plugin-admin', plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery', 'jquery-ui-sortable'), '1.0', true);
        wp_enqueue_style('vpfpff-plugin-admin-style', plugin_dir_url(__FILE__) . 'css/admin.css');
    }
}
add_action('admin_enqueue_scripts', 'vpfpff_plugin_enqueue_assets');

// Render the admin page
function wvpfpff_plugin_render_admin_page() {
    $items = array('Item 1', 'Item 2', 'Item 3');

    echo '<div class="wrap">';
    echo '<h2>Rearrange Items</h2>';
    echo '<ul id="sortable-list">';

    foreach ($items as $item) {
        echo '<li class="sortable-item">' . esc_html($item) . '</li>';
    }

    echo '</ul>';
    echo '</div>';
}
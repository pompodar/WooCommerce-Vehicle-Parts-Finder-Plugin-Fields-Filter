<?php              
// Specify the custom taxonomy
$taxonomy = 'product_make';

$make_options = array();
$direct_children = array();
$grandchildren = array();

// Get all terms from the custom taxonomy
$all_terms = get_terms($taxonomy);

if (!empty($all_terms) && !is_wp_error($all_terms)) {
    foreach ($all_terms as $term) {
        // Check if the term is a parent (has no parent)
        if ($term->parent == 0) {
            // Add parents to the result array
            $make_options[] = $term->name;            
        }
    }
}
?>
<form id="custom-filter-form">
    <div id="wvpfpff-spinner"></div>
    <!-- Make Filter -->
    <select id="make-filter" name="make">
        <option value="all" class="wvpfpff-disabled">Make</option>
        <option value="all">All Makes</option>
        <?php
        foreach ($make_options as $value) {
            echo '<option value="' . esc_attr($value) . '">' . esc_html($value) . '</option>';
        }
        ?>
        <!-- Add more options as needed -->
    </select>

    <!-- Model Filter (Initially Disabled) -->
    <select data-model-order-by="<?php echo $model_order_by; ?>" id="model-filter" name="model" disabled>
        <option value="all" class="wvpfpff-disabled">Model</option>
        <option value="all">All Models</option>
        <!-- Options for models will be loaded dynamically using JavaScript -->
    </select>

    <!-- Year Filter (Initially Disabled) -->
    <select id="year-filter" name="year" disabled>
        <option value="all" class="wvpfpff-disabled">Year</option>
        <option value="all">All Years</option>

        <!-- Options for years will be loaded dynamically using JavaScript -->
    </select>

    <!-- Category Filter (Initially Disabled) -->
    <select data-category-order-by="<?php echo $category_order_by; ?>" id="category-filter" name="category" disabled>
        <option value="all" class="wvpfpff-disabled">Category</option>
        <option value="all">All Categories</option>

        <!-- Options for categories will be loaded dynamically using JavaScript -->
    </select>

    <!-- Brand Filter (Initially Disabled) -->
    <select data-tag-order-by="<?php echo $brand_order_by; ?>" id="brand-filter" name="brand" disabled>
        <option value="all" class="wvpfpff-disabled">Brand</option>
        <option value="all">All Brands</option>
        <!-- Options for brands will be loaded dynamically using JavaScript -->
    </select>

    <input type="submit" value="Search">
    <button id="reset-filters">Reset</button>
</form>

<div id="filter-results">
    <!-- Results will be displayed here using JavaScript -->
</div>
<?php
// Specify the custom taxonomy
$taxonomy = 'product_make'; 

$make_options = array();
$direct_children = array();

// Get all terms from the custom taxonomy
$all_terms = get_terms($taxonomy);

if (!empty($all_terms) && !is_wp_error($all_terms)) {
    foreach ($all_terms as $term) {
        // Check if the term is a parent (has no parent)
        if ($term->parent == 0) {
            // Add parents to the result array
            $make_options[] = $term->name;
            
            // Get the direct child terms for the parent
            $child_term_args = array(
                'taxonomy' => $taxonomy,
                'parent' => $term->term_id,
            );
            
            $child_terms = get_terms($child_term_args);

            $parent_children_names = array();
            foreach ($child_terms as $child_term) {
                $parent_children_names[] = $child_term->name;
            }

            // Add direct children to the result array
            $direct_children[] = array(
                'parent_name' => $term->name,
                'children_names' => $parent_children_names,
            );
        }
    }
}
?>
<form id="custom-filter-form">
    <div id="wvpfpff-spinner"></div>
    <!-- Make Filter -->
    <select id="make-filter" name="make">
        <option value="" disabled>Make</option>
        <?php
        foreach ($make_options as $value) {
            echo '<option value="' . esc_attr($value) . '">' . esc_html($value) . '</option>';
        }
        ?>
        <!-- Add more options as needed -->
    </select>

    <!-- Model Filter (Initially Disabled) -->
    <select id="model-filter" name="model" disabled>
        <option value="" disabled>Model</option>
        <!-- Options for models will be loaded dynamically using JavaScript -->
    </select>

    <!-- Year Filter (Initially Disabled) -->
    <select id="year-filter" name="year" disabled>
        <option value="" disabled>Year</option>
        <!-- Options for years will be loaded dynamically using JavaScript -->
    </select>

    <!-- Category Filter (Initially Disabled) -->
    <select id="category-filter" name="category" disabled>
        <option value="" disabled>Category</option>
        <!-- Options for categories will be loaded dynamically using JavaScript -->
    </select>

    <!-- Brand Filter (Initially Disabled) -->
    <select id="brand-filter" name="brand" disabled>
        <option value="" disabled>Brand</option>
        <!-- Options for brands will be loaded dynamically using JavaScript -->
    </select>

    <input type="submit" value="Search">
</form>

<div id="filter-results">
    <!-- Results will be displayed here using JavaScript -->
</div>
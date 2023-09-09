<?php
?>
<form id="custom-filter-form">
    <!-- Make Filter -->
    <select id="make-filter" name="make">
        <option value="" disabled>Make</option>
        <option value="make1">Make 1</option>
        <option value="make2">Make 2</option>
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
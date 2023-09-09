<?php
?>
<form id="custom-filter-form">
    <!-- Make Filter -->
    <label for="make-filter">Make:</label>
    <select id="make-filter" name="make">
        <option value="make1">Make 1</option>
        <option value="make2">Make 2</option>
        <!-- Add more options as needed -->
    </select>

    <!-- Model Filter (Initially Disabled) -->
    <label for="model-filter">Model:</label>
    <select id="model-filter" name="model" disabled>
        <!-- Options for models will be loaded dynamically using JavaScript -->
    </select>

    <!-- Year Filter (Initially Disabled) -->
    <label for="year-filter">Year:</label>
    <select id="year-filter" name="year" disabled>
        <!-- Options for years will be loaded dynamically using JavaScript -->
    </select>

    <!-- Category Filter (Initially Disabled) -->
    <label for="category-filter">Category:</label>
    <select id="category-filter" name="category" disabled>
        <!-- Options for categories will be loaded dynamically using JavaScript -->
    </select>

    <!-- Brand Filter (Initially Disabled) -->
    <label for="brand-filter">Brand:</label>
    <select id="brand-filter" name="brand" disabled>
        <!-- Options for brands will be loaded dynamically using JavaScript -->
    </select>

    <input type="submit" value="Search">
</form>

<div id="filter-results">
    <!-- Results will be displayed here using JavaScript -->
</div>
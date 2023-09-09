jQuery(document).ready(function ($) {
    // Function to load options for the dependent filters
    function loadOptions(filter, parentValue) {
        var ajaxurl = custom_filter_vars.ajaxurl;

        $.ajax({
            url: ajaxurl, // This is the WordPress AJAX URL
            type: "POST",
            data: {
                action: "custom_filter_ajax_handler", // The AJAX action you defined in your plugin
                filter: filter,
                parent: parentValue,
            },
            success: function (response) {
                $("#" + filter + "-filter").html(response);
                $("#" + filter + "-filter").prop("disabled", false);
            },
        });
    }

    // When the 'Make' filter changes, load 'Model' options
    $("#make-filter").on("change", function () {
        var makeValue = $(this).val();
        if (makeValue) {
            loadOptions("model", makeValue);
        } else {
            $("#model-filter").html(
                '<option value="">Select Make First</option>'
            );
            $("#model-filter").prop("disabled", true);
        }
    });

    // Similarly, implement logic for other dependent filters (Year, Category, Brand)

    // Prevent the form from submitting normally
    $("#custom-filter-form").on("submit", function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var ajaxurl = custom_filter_vars.ajaxurl;

        // Make an AJAX request to filter products
        $.ajax({
            url: ajaxurl, // This is the WordPress AJAX URL
            type: "POST",
            data: {
                action: "custom_filter_ajax_handler", // The AJAX action you defined in your plugin
                formData: formData,
            },
            success: function (response) {
                $("#filter-results").html(response);
            },
        });
    });
});

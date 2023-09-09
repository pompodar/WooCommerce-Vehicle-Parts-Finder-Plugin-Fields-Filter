jQuery(document).ready(function ($) {
    // Access the AJAX URL from the localized variable
    var ajaxurl = custom_filter_vars.ajaxurl;

    // Function to set a cookie with the selected values
    function setCookie(name, value) {
        document.cookie = name + "=" + value + ";path=/";
    }

    // Function to get a cookie value
    function getCookie(name) {
        var match = document.cookie.match(new RegExp(name + "=([^;]+)"));
        return match ? match[1] : null;
    }

    // Function to load options for the dependent filters
    function loadOptions(filter, parentValue) {
        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: "custom_filter_ajax_handler",
                filter: filter,
                parent: parentValue,
            },
            success: function (response) {
                $("#" + filter + "-filter").html(response);
                $("#" + filter + "-filter").prop("disabled", false);
            },
        });
    }

    // When the page loads, populate the form fields with saved values
    $("#make-filter").val(getCookie("make"));
    $("#model-filter").val(getCookie("model"));
    $("#year-filter").val(getCookie("year"));
    $("#category-filter").val(getCookie("category"));
    $("#brand-filter").val(getCookie("brand"));

    // Enable dependent filters if the previous filter has a value
    if (getCookie("make")) {
        loadOptions("model", getCookie("make"));
        $("#model-filter").prop("disabled", false);
    }

    if (getCookie("model")) {
        loadOptions("year", getCookie("model"));
        $("#year-filter").prop("disabled", false);
    }

    if (getCookie("year")) {
        loadOptions("category", getCookie("year"));
        $("#category-filter").prop("disabled", false);
    }

    if (getCookie("category")) {
        loadOptions("brand", getCookie("category"));
        $("#brand-filter").prop("disabled", false);
    }

    // When a filter changes, save the selection in a cookie
    $("#make-filter, #model-filter, #year-filter, #category-filter").on(
        "change",
        function () {
            var filterId = $(this).attr("id");
            var filterValue = $(this).val();

            // Save the selection in a cookie
            setCookie(filterId, filterValue);

            // Enable and load options for the next filter
            var nextFilterId = "";
            if (filterId === "make-filter") {
                nextFilterId = "model-filter";
            } else if (filterId === "model-filter") {
                nextFilterId = "year-filter";
            } else if (filterId === "year-filter") {
                nextFilterId = "category-filter";
            } else if (filterId === "category-filter") {
                nextFilterId = "brand-filter";
            }

            if (nextFilterId) {
                loadOptions(nextFilterId.replace("-filter", ""), filterValue);
                $("#" + nextFilterId).prop("disabled", false);
            }
        }
    );

    // Prevent the form from submitting normally
    $("#custom-filter-form").on("submit", function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        // Make an AJAX request to filter products
        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: "custom_filter_ajax_handler",
                formData: formData,
            },
            success: function (response) {
                $("#filter-results").html(response);
            },
        });
    });
});

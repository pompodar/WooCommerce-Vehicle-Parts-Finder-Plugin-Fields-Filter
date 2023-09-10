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
        // Show the spinner while loading
        $("#wvpfpff-spinner").show();

        const make = $("#make-filter").val();
        
        // TODO not 100% sure if correct
        const model = $("#model-filter").val() ? $("#model-filter").val() : getCookie("model-filter");

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: "custom_filter_itself_ajax_handler",
                filter: filter,
                parent: parentValue,
                make,
                model
            },
            success: function (response) {
                $("#" + filter + "-filter").html(response);
                $("#" + filter + "-filter").prop("disabled", false);

                // TODO not 100% sure if correct
                // When the page loads, populate the form fields with saved values
                $("#make-filter").val(getCookie("make-filter"));
                $("#model-filter").val(getCookie("model-filter"));
                $("#year-filter").val(getCookie("year-filter"));
                $("#category-filter").val(getCookie("category-filter"));
                $("#brand-filter").val(getCookie("brand-filter"));

                // Hide the spinner after loading
                $("#wvpfpff-spinner").hide();
            },
            complete: function () {
                // Hide the spinner even if there was an error
                $("#wvpfpff-spinner").hide();
            },
        });
    }

    // When the page loads, populate the form fields with saved values
    $("#make-filter").val(getCookie("make-filter"));
    $("#model-filter").val(getCookie("model-filter"));
    $("#year-filter").val(getCookie("year-filter"));
    $("#category-filter").val(getCookie("category-filter"));
    $("#brand-filter").val(getCookie("brand-filter"));

    // Enable dependent filters if the previous filter has a value
    if (getCookie("make-filter")) {
        loadOptions("model", getCookie("make-filter"));
        $("#model-filter").prop("disabled", false);
    }

    if (getCookie("model-filter")) {
        loadOptions("year", getCookie("model-filter"));
        $("#year-filter").prop("disabled", false);
    }

    if (getCookie("year-filter")) {
        loadOptions("category", getCookie("year-filter"));
        $("#category-filter").prop("disabled", false);
    }

    if (getCookie("category-filter")) {
        loadOptions("brand", getCookie("category-filter"));
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

        // Show the spinner while loading
        $("#wvpfpff-spinner").show();

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
              
                // Hide the spinner after loading
                $("#wvpfpff-spinner").hide();
            },
            complete: function () {
                // Hide the spinner even if there was an error
                $("#wvpfpff-spinner").hide();
            },
        });
    });
});

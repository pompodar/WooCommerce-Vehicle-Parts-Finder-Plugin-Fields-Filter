jQuery(document).ready(function ($) {
    // Access the AJAX URL from the localized variable
    const ajaxurl = custom_filter_vars.ajaxurl;

    // Function to set a cookie with the selected values
    function setCookie(name, value) {
        document.cookie = name + "=" + value + ";path=/";
    }

    // Function to get a cookie value
    function getCookie(name) {
        const match = document.cookie.match(new RegExp(name + "=([^;]+)"));
        return match ? match[1] : null;
    }

    // Function to load options for the dependent filters
    function loadOptions(filter, parentValue, onPageLoad) {
        // Show the spinner while loading
        $("#wvpfpff-spinner").show();

        const make = $("#make-filter").val();
        
        // TODO not 100% sure if correct
        const model = $("#model-filter").val() ? $("#model-filter").val() : getCookie("model-filter");
        const year = $("#year-filter").val()
                    ? $("#year-filter").val()
                    : getCookie("year-filter");

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: "custom_filter_itself_ajax_handler",
                filter: filter,
                parent: parentValue,
                make,
                model,
                year
            },
            success: function (response) {
                $("#" + filter + "-filter").html(response);
                $("#" + filter + "-filter").prop("disabled", false);
                console.log(response);
               
                if (onPageLoad) {
                    const cookieName = filter + "-filter";
                    // $("#" + cookieName).val(getCookie(cookieName));
                }

                // Hide the spinner after loading
                $("#wvpfpff-spinner").hide();
            },
            complete: function () {
                // Hide the spinner even if there was an error
                $("#wvpfpff-spinner").hide();
            },
        });
    }

    // When the page loads, populate the "make" form fields with saved values
    // $("#make-filter").val(getCookie("make-filter"));


    // Enable dependent filters if the previous filter has a value
    if (getCookie("make-filter")) {
        // loadOptions("model", getCookie("make-filter"), 1);
        $("#model-filter").prop("disabled", false);
    }

    if (getCookie("model-filter")) {
        // loadOptions("year", getCookie("model-filter"), 1);
        $("#year-filter").prop("disabled", false);
    }

    if (getCookie("year-filter")) {
        // loadOptions("category", getCookie("year-filter"), 1);
        $("#category-filter").prop("disabled", false);
    }

    if (getCookie("category-filter")) {
        // loadOptions("brand", getCookie("category-filter"), 1);
        $("#brand-filter").prop("disabled", false);
    }

    // When a filter changes, save the selection in a cookie
    $("#make-filter, #model-filter, #year-filter, #category-filter").on(
        "change",
        function () {
            const filterId = $(this).attr("id");
            const filterValue = $(this).val();

            // Save the selection in a cookie
            //setCookie(filterId, filterValue);

            // Enable and load options for the next filter
            let nextFilterId = "";
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
                loadOptions(nextFilterId.replace("-filter", ""), filterValue, 0);
                $("#" + nextFilterId).prop("disabled", false);
            }
        }
    );

    // Prevent the form from submitting normally
    $("#custom-filter-form").on("submit", function (e) {
        e.preventDefault();

        // Show the spinner while loading
        $("#wvpfpff-spinner").show();

        const formData = $(this).serialize();

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

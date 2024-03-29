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
        return new Promise((resolve, reject) => {
            // Show the spinner while loading and make the form unclikable
            $("#wvpfpff-spinner").show();

            // Disable keyboard
            $("#custom-filter-form").keydown(function (event) {
                return false;
            });

            $("#custom-filter-form").addClass("wvpfpff-unclickable");

            const make = $("#make-filter").val();

            let model, year, category, brand;

            if ($("#model-filter").val()) {
                model = $("#model-filter").val();
            } else if (getCookie("model-filter")) {
                model = getCookie("model-filter");
            } else {
                model = "all";
            }

            if ($("#year-filter").val()) {
                year = $("#year-filter").val();
            } else if (getCookie("year-filter")) {
                year = getCookie("year-filter");
            } else {
                year = "all";
            }

            if ($("#category-filter").val()) {
                category = $("#category-filter").val();
            } else if (getCookie("category-filter")) {
                category = getCookie("category-filter");
            } else {
                category = "all";
            }

            if ($("#brand-filter").val()) {
                brand = $("#brand-filter").val();
            } else if (getCookie("brand-filter")) {
                brand = getCookie("brand-filter");
            } else {
                brand = "all";
            }

            const modelOrderBy = $("#model-filter").data() ? $("#model-filter").data().modelOrderBy : null;
            const categoryOrderBy =
                $("#category-filter").data() ? $("#category-filter").data().categoryOrderBy : null;
            const brandOrderBy = $("#brand-filter").data() ? $("#brand-filter").data().tagOrderBy : null;

            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: "custom_filter_itself_ajax_handler",
                    filter: filter,
                    parent: parentValue,
                    make,
                    model,
                    year,
                    category,
                    brand,
                    model_order_by: modelOrderBy,
                    category_order_by: categoryOrderBy,
                    brand_order_by: brandOrderBy,
                },
                cache: false,
                success: function (response) {
                    $("#" + filter + "-filter").html(response);
                    $("#" + filter + "-filter").prop("disabled", false);

                    if (onPageLoad) {
                        const cookieName = filter + "-filter";
                        if (getCookie(cookieName)) {
                            $("#" + cookieName).val(getCookie(cookieName));
                        } else {
                            $("#" + cookieName).val("all");
                        }
                    }

                    // Hide the spinner after loading and make the form clickable
                    $("#wvpfpff-spinner").hide();
                    $("#custom-filter-form").removeClass("wvpfpff-unclickable");

                    // Resolve the promise when the operation is complete
                    resolve();
                },
                complete: function () {
                    // Hide the spinner even if there was an error and make the form clickable
                    $("#wvpfpff-spinner").hide();
                    $("#custom-filter-form").removeClass("wvpfpff-unclickable");
                },
            });
        });
    }

    // When the page loads, populate the "make" form fields with saved values
    async function onPageLoadFunc() {
        if (getCookie("make-filter")) {
            $("#make-filter").val(getCookie("make-filter"));

            await $("#model-filter").prop("disabled", false);
            await loadOptions("model", getCookie("make-filter"), 1);
        }

        if (getCookie("model-filter")) {
            await loadOptions("year", getCookie("model-filter"), 1);
            $("#year-filter").prop("disabled", false);
        }

        if (getCookie("year-filter")) {
            await loadOptions("category", getCookie("year-filter"), 1);
            $("#category-filter").prop("disabled", false);
        }

        if (getCookie("category-filter")) {
            await loadOptions("brand", getCookie("category-filter"), 1);
            $("#brand-filter").prop("disabled", false);
        }
    }

    onPageLoadFunc();

    // When a filter changes, save the selection in a cookie and load dependent options
    $("#make-filter, #model-filter, #year-filter, #category-filter").on(
        "change",
        async function () {
            const filterId = $(this).attr("id");
            const filterValue = $(this).val();

            // Save the selection in a cookie
            setCookie(filterId, filterValue);

            // In case not all is selected
            if (filterValue != "all") {
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
                    // Clear the next filters' values except the first next one
                    $(`#${nextFilterId}`).val("all");
                    $(`#${nextFilterId}`)
                        .nextAll("select")
                        .val("all")
                        .prop("disabled", true);

                    // Clear the corresponding cookies for the next filters
                    $(`#${nextFilterId}`)
                        .nextAll("select")
                        .each(function () {
                            const cookieName = $(this).attr("id");
                            clearCookie(cookieName);
                        });

                    await loadOptions(
                        nextFilterId.replace("-filter", ""),
                        filterValue,
                        0
                    );
                }
            }
        }
    );

    // When a filter changes for brand, save the selection in a cookie
    $("#brand-filter").on("change", async function () {
        const filterId = $(this).attr("id");
        const filterValue = $(this).val();

        // Save the selection in a cookie
        setCookie(filterId, filterValue);
    });

    // Prevent the form from submitting normally
    $("#custom-filter-form").on("submit", function (e) {
        e.preventDefault();

        // Show the spinner while loading
        $("#wvpfpff-spinner").show();

        $("#custom-filter-form").keydown(function (event) {
            return false;
        });

        let formData = $(this).serialize();

        // Make an AJAX request to filter products
        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: "custom_filter_ajax_handler",
                formData: formData,
            },
            cache: false,
            success: function (response) {
                $("#filter-results").html(response);

                // After the first AJAX call is complete, trigger the second AJAX call
                loadRelatedProducts(formData);
            },
        });
    });

    // Function to make an AJAX request for related products
    function loadRelatedProducts(formData) {
        console.log(formData);
        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: "custom_filter_ajax_related_products_handler",
                formData: formData,
            },
            cache: false,
            success: function (response) {
                $("#filter-results-related-products").html(response);

                // Hide the spinner
                $("#wvpfpff-spinner").hide();
            },
        });
}

    // Function to clear a cookie by name
    function clearCookie(name) {
        document.cookie =
            name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }

    // Handle the click event for the reset button
    $("#reset-filters").on("click", function () {
        // Clear the selected filter values
        $(
            "#make-filter, #model-filter, #year-filter, #category-filter, #brand-filter"
        ).val("all");

        // Clear the corresponding cookies
        clearCookie("make-filter");
        clearCookie("model-filter");
        clearCookie("year-filter");
        clearCookie("category-filter");
        clearCookie("brand-filter");

        // Disable dependent filters and clear their options
        $("#model-filter, #year-filter, #category-filter, #brand-filter")
            .prop("disabled", true);
    });
});

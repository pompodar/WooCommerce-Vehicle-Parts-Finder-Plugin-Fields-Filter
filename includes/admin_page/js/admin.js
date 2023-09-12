// Models
jQuery(document).ready(function ($) {
    // Toggle the form for each parent term and its children
    $(".toggle-form-models").on("click", function () {
        const form = $(this).next(".sortable-form-models");
        form.slideToggle();

        form.prev().text() == "Show Models"
            ? form.prev().text("Hide Models")
            : form.prev().text("Show Models");
    });

    // Handle the sortable lists within each form
    $(".sortable-form-models").each(function () {
        const form = $(this);
        const sortableList = form.find(".sortable-list-models");
        const make = form.find("input[name=parent_term_name]").val();

        sortableList.sortable({
            update: function () {
                // Update the order when items are rearranged
                const order = [];
                form.find(".wvpfppff-admin-spinner-models").show();

                sortableList.find(".sortable-item").each(function () {
                    order.push($(this).text());
                });

                console.log(order);

                // Send the updated order via AJAX
                $.ajax({
                    url: wvpfpff_filter_vars.ajaxurl,
                    type: "POST",
                    data: {
                        action: "wvpfpff_plugin_update_model_item_order",
                        new_order: order,
                        make,
                    },
                    success: function (response) {
                        if (response.success) {
                            // Display the success message to the user
                            alert(response.data); // Show a pop-up message
                            // You can also display the message in a div on the page if needed
                        } else {
                            // Display the error message to the user
                            alert(response.data); // Show a pop-up message
                            // You can also display the message in a div on the page if needed
                        }
                        form.find(".wvpfppff-admin-spinner-models").hide();
                    },
                });
            },
        });
    });
});

jQuery(function ($) {
    $("#sortable-list").sortable({
        update: function (event, ui) {
            var newOrder = $(this).sortable("toArray");
            $.post(ajaxurl, {
                action: "my_plugin_update_item_order",
                new_order: newOrder,
            });
        },
    });
    $("#sortable-list").disableSelection();
});
alert(1)

window.CMBFT = window.CMBFT || {};
(function (window, document, $, cmbft) {
    "use strict";

    cmbft.init = function () {
        // Autocomplete on already created inputs
        $("input.post-link").each(function () {
            cmbft.autoComplete(this);
        });

        // Clean-up value on new created inputs
        $(".cmb-type-group").on("cmb2_add_row", cmbft.cleanUp);

        // Autocomplete on newly created inputs
        $(".cmb-type-group").on("cmb2_add_row", cmbft.addRow);
        // $(".cmb-type-group").on(
        //     "click",
        //     "button.cmb-add-group-row",
        //     cmbft.autoComplete
        // );

        // Delete values
        $(".cmb-type-group").on(
            "click",
            ".cmb-type-post-link span.delete",
            cmbft.removeValues
        );
        $(".cmb-type-post-link span.delete").on("click", cmbft.removeValues);

        // Media modal
        // $(document).on("cmb_media_modal_init", cmbft.modalInit);
        if ($("body.nav-menus-php").length) {
            cmbft.$menuItems = $(".menu-edit ul.menu li.menu-item");
            cmbft.setInputNames();
            $(document).on("cmb_media_modal_select", cmbft.modalSelect);
        }
    };

    cmbft.addRow = function (ev, row) {
        cmbft.autoComplete($(row).find(".post-link"));
    };

    cmbft.autoComplete = function (el) {
        let inputAutoCom = $(el).autocomplete({
            minLength: 3,
            source: cmbft.getSource(el),
            select: function (event, ui) {
                let input = this;
                let spanValue = $(this).siblings("span.post-link-value");
                let inputLabel = $(this).siblings("input.post-link-label");
                let inputValue = $(this).siblings("input.post-link-value");
                input.value = ui.item.label;
                spanValue.html(ui.item.value);
                inputLabel.val(ui.item.label);
                inputValue.val(ui.item.value);
                return false;
            },
        });
        inputAutoCom.autocomplete("instance")._renderItem = function (
            ul,
            item
        ) {
            let label = $("<span></span>").addClass("label").text(item.label);
            let value = $("<span></span>").addClass("value").text(item.value);
            return $("<li>")
                .append(
                    "<div>" +
                        label[0].outerHTML +
                        "<br>" +
                        value[0].outerHTML +
                        "</div>"
                )
                .appendTo(ul);
        };
    };

    cmbft.getSource = function (el) {
        return JSON.parse($(el).attr("data-source"));
    };

    cmbft.cleanUp = function (e) {
        $("input.post-link").each(function () {
            if (!$(this).siblings("input.post-link-value").val()) {
                $(this).siblings("span.post-link-value").text("");
                $(this).val("");
            }
        });
    };

    cmbft.removeValues = function (e) {
        $(this).siblings("input.post-link").val("");
        $(this).siblings("span.post-link-value").text("");
        $(this).siblings("input.post-link-label").val("");
        $(this).siblings("input.post-link-value").val("");
    };

    cmbft.setInputNames = function () {
        $.each(cmbft.$menuItems, function (i, menu) {
            let itemID = $(menu).find(".menu-item-data-db-id").val();
            // Add menu item id to file id input name
            let fileIDName = $(menu)
                .find(".cmb-type-file .cmb2-upload-file-id")
                .attr("name");
            $(menu)
                .find(".cmb-type-file .cmb2-upload-file-id")
                .attr("name", fileIDName + "[" + itemID + "]");
            // Add menu item id to file input name
            let fileName = $(menu)
                .find(".cmb-type-file .cmb2-upload-file")
                .attr("name");
            $(menu)
                .find(".cmb-type-file .cmb2-upload-file")
                .attr("name", fileName + "[" + itemID + "]");
        });
    };

    cmbft.modalSelect = function (evt, selection, media) {
        if (evt) {
            // let $input = $("input[name='" + media.fieldName + "']");
            // $.each(media.frames, function (i, el) {
            //     var attachment = el.state().get("selection").first().toJSON();
            //     $input.attr("value", attachment.url);
            //     // let sel = el.state().get("selection");
            //     // var attachment_ids = sel
            //     //     .map(function (attachment) {
            //     //         attachment = attachment.toJSON();
            //     //         return attachment.id;
            //     //     })
            //     //     .join();
            // });
        }
    };

    $(document).ready(function () {
        $(cmbft.init);
    });
})(window, document, jQuery, window.CMBFT);

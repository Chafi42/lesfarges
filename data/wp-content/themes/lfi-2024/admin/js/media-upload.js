jQuery(function ($) {
    // Set all variables to be used in scope
    var frame,
        addImgLink = $(".upload-custom-img"),
        delImgLink = $(".delete-custom-img");

    // ADD IMAGE LINK
    addImgLink.on("click", function (event) {
        event.preventDefault();

        let $parent = $(this).parents("td");
        let curContainer = $parent.find(".custom-img-container");
        let curIdInput = $parent.find(".custom-img-id");
        let curAddLink = $parent.find(".upload-custom-img");
        let curDelLink = $parent.find(".delete-custom-img");

        // If the media frame already exists, reopen it.
        if (frame) {
            frame.open();
            return;
        }

        // Create a new media frame
        frame = wp.media({
            title: "Select or Upload Media Of Your Chosen Persuasion",
            button: {
                text: "Use this media",
            },
            multiple: false, // Set to true to allow multiple files to be selected
        });

        // When an image is selected in the media frame...
        frame.on("select", function () {
            // Get media attachment details from the frame state
            var attachment = frame.state().get("selection").first().toJSON();

            // Send the attachment URL to our custom image input field.
            curContainer.append(
                '<img src="' +
                    // attachment.sizes.thumbnail.url +
                    attachment.url +
                    '" alt="" style="max-width:100%;"/>'
            );

            // Send the attachment id to our hidden input
            curIdInput.val(attachment.id);

            // Hide the add image link
            curAddLink.addClass("hidden");

            // Unhide the remove image link
            curDelLink.removeClass("hidden");
        });

        // Finally, open the modal on click
        frame.open();
    });

    // DELETE IMAGE LINK
    delImgLink.on("click", function (event) {
        event.preventDefault();

        let $parent = $(this).parents("td");
        let curContainer = $parent.find(".custom-img-container");
        let curIdInput = $parent.find(".custom-img-id");
        let curAddLink = $parent.find(".upload-custom-img");
        let curDelLink = $parent.find(".delete-custom-img");

        // Clear out the preview image
        curContainer.html("");

        // Un-hide the add image link
        curAddLink.removeClass("hidden");

        // Hide the delete image link
        curDelLink.addClass("hidden");

        // Delete the image id from the hidden input
        curIdInput.val("");
    });

    // icon picker
    var iconPicker = $(".icp").iconpicker({});

    iconPicker.on("iconpickerSelected", function (event) {
        /* event.iconpickerValue */
        $(this)
            .siblings(".iconpicker-component")
            .find("input")
            .attr("value", event.iconpickerValue);
    });
});

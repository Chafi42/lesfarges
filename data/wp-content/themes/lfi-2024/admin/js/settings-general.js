jQuery(document).ready(function ($) {
    // Range inputs
    if ($("input[type=range]").length) {
        $(document).on("input change", "input[type=range]", function (e) {
            $(this).siblings("span.output").html($(this).val());
        });
    }

    // Select inputs
    $.each($(".select-2"), function (index, select) {
        let placeholder = $(select).find("option:first-child").text();
        $(select).select2({
            placeholder: placeholder,
            allowClear: true,
            minimumResultsForSearch: -1,
        });
    });
});

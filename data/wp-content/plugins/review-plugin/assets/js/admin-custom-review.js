(function ($) {
    $(document).ready(function () {
        $(".wporg_avis-star").on("click", function () {
            let clickedStarIndex = $(this).index();
            let isSelected = $(this).hasClass("fa-solid");
            // if (isSelected === true && $(this).prev().hasClass("fa-solid")) {
            if (
                clickedStarIndex == 1 &&
                isSelected &&
                !$(this).next().hasClass("fa-solid")
            ) {
                $(".wporg_field").val(0);
                $(this).removeClass("fa-solid").addClass("fa-regular");
            } else {
                $(".wporg_field").val(clickedStarIndex);

                $(this).removeClass("fa-regular").addClass("fa-solid");

                $(this)
                    .prevAll()
                    .removeClass("fa-regular")
                    .addClass("fa-solid");
            }

            $(this).nextAll().removeClass("fa-solid").addClass("fa-regular");

            $(".wporg_field").val($(".fa-solid.wporg_avis-star").length);
        });
    });
})(jQuery);

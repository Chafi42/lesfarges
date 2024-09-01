(function ($) {
    $(document).ready(function () {
        const myCollapsible = $("#menu-lesfarges");
        const burgerSpans = $(".burger-menu span");

        myCollapsible.on("show.bs.collapse", function () {
            $(burgerSpans)
                .removeClass("has-violet-background-color")
                .addClass("bg-white");
        });

        myCollapsible.on("hidden.bs.collapse", function () {
            $(burgerSpans)
                .removeClass("bg-white")
                .addClass("has-violet-background-color");
        });
    });
    // <-------------------------------------------------------------------------------------->
    // Survol group is-style-hover
    let groups = $(".wp-block-group.is-style-hover");
    $.each(groups, function (i, group) {
        $(group).find("figure").first().addClass("show");
        $(group).find("figure").last().addClass("show").removeClass("show");
        $(group).hover(
            function () {
                $(group).find("figure").toggleClass("show");
            },
            function () {
                $(group).find("figure").toggleClass("show");
            }
        );
    });
})(jQuery);

(function ($) {
    window.addEventListener("load", function (event) {
        console.log("Toutes les ressources sont chargées !");
        var preloader = document.getElementById("lfi-loader");
        if (!preloader.classList.contains("done")) {
            preloader.classList.add("done");
            $("body").removeClass("overflow-hidden");
            // smoothScrolling();
        }
    });
})(jQuery);

jQuery(document).ready(function ($) {
    var ajaxLoader = $(".wpcf7-form.init span.ajax-loader");
    var container =
        '<div class="container-fluid h-100"><div class="row h-100 align-items-center justify-content-center"><div class="lfi-loader"></div></div></div>';
    $(ajaxLoader).addClass("loader-body").prepend(container);
    $(".wpcf7-form.init").prepend($(ajaxLoader));
});


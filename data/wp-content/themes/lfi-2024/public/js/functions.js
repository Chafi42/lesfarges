function smoothScrolling(hash) {
    if (typeof hash !== "undefined") {
        hash = hash;
    } else {
        hash = window.location.hash;
        window.scrollTo(0, 0);
    }

    let headerH = 90;
    // if (scrollTo_params.headerH) {
    //     headerH = parseInt(scrollTo_params.headerH);
    // }
    // Using jQuery's animate() method to add smooth page scroll
    // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
    if (jQuery(hash).length) {
        // Ne fonctionne pas pour les pages produit woocommerce
        if (!jQuery("body").hasClass("single-product")) {
            jQuery("html, body").animate(
                {
                    scrollTop: jQuery(hash).offset().top - headerH,
                },
                1000,
                function () {
                    // Add hash (#) to URL when done scrolling (default click behavior)
                    // window.location.hash = hash;
                    window.history.pushState("", "", hash);
                }
            );
        }
    }
}
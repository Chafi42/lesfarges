window.lfigut = window.lfigut || {};
(function (window, document, $, lfigut) {
    "use strict";
    
    lfigut.init = function () {
        // Remove 'hndle' class to .cmb2-postbox h2
        $.each($(".is-side .cmb2-postbox h2.hndle"), function (i, el) {
            $(el).removeClass("hndle");
        }) 
    };
    
    $(document).ready(function () {
        $(lfigut.init);
    });
})(window, document, jQuery, window.lfigut);
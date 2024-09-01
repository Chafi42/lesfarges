function smoothScrolling(hash, el) {
	if (typeof hash !== "undefined") {
		hash = hash;
	} else {
		console.log("no hash");
		hash = window.location.hash;
		// Prevent browser auto-scroll to "hash"
		window.scrollTo(0, 0);
	}

	var headerH = 90;
	if (jQuery("nav.fixed-top")) {
		headerH = jQuery("nav.fixed-top").outerHeight(true);
	}

	// if (scrollTo_params.headerH) {
	//     headerH = parseInt(scrollTo_params.headerH);
	// }
	// Using jQuery's animate() method to add smooth page scroll
	// The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
	if (jQuery(hash).length) {
		// Ne fonctionne pas pour les pages produit woocommerce
		if (!jQuery("body").hasClass("single-product")) {
			// jQuery.event.trigger({
			//     type: "start-smoothscroll",
			// });
			jQuery(el).trigger("start-smoothscrolling");
			let scrollTo = jQuery(hash).offset().top - headerH;
			console.log("scrollTo :", scrollTo);
			jQuery("html").animate(
				{
					scrollTop: scrollTo,
				},
				1000,
				"easeOutQuart",
				function () {
					// Add hash (#) to URL when done scrolling (default click behavior)
					window.history.pushState("", "", hash);
					// jQuery.event.trigger({
					//     type: "done-smoothscroll",
					// });
					jQuery(el).trigger("stop-smoothscrolling");
				}
			);
		}
	}
}

(function ($) {
	window.addEventListener("load", function (event) {
		console.log("Toutes les ressources sont chargées !");
		var preloader = document.getElementById("lfi-loader");
		if (!preloader.classList.contains("done")) {
			preloader.classList.add("done");
			$("body").removeClass("overflow-hidden");
			let res = $.event.trigger({
				type: "loader-done",
			});
			// smoothScrolling();
		}
	});
	function bodyMargin(element, dir) {
		let elementH = $(element).outerHeight(true);
		switch (dir) {
			case "bottom":
				$("body").css("margin-bottom", elementH);
				break;

			case "top":
				$("body").css("margin-top", elementH);
				break;
		}
	}

	// Click and scroll
	// if (window.location.hash) {
	//     var target = window.location.hash;
	//     window.location.hash = "";
	//     smoothScrolling(target)
	// }

	$(document).on("loader-done", function (e) {
		// Sticky Footer
		// Calculate the "body" "margin-bottom"
		if ($("footer.stick-to-bottom").length) {
			let $el = $("footer.stick-to-bottom");
			bodyMargin($el, "bottom");
			$(window).resize(function () {
				bodyMargin($el, "bottom");
			});
		}
		// Nav fixed to the top
		// Calculate the "body" "margin-top"
		// Only if nav has "data-margin" attributes equals to "true|1"
		if ($("nav.fixed-top").attr("data-margin")) {
			bodyMargin($("nav.fixed-top:visible"), "top");
			$(window).resize(function () {
				bodyMargin($("nav.fixed-top:visible"), "top");
			});
		}
		// Toggle full-height-nav
		$("[data-toggle='nav']").click(function () {
			console.log("data-toggle=nav click");
			var selector = $(this).data("target");
			$(selector).toggleClass("show");
			$(this).toggleClass("collapsed");
		});

		// Smooth Scroll at Load
		// Prevent auto-scroll before loading
		window.scrollTo(0, 0);
		let curhash = window.location.hash;
		if ($(curhash).length) {
			history.pushState(null, null, " ");
			setTimeout(() => {
                smoothScrolling(curhash, $(curhash))
			}, 600);
		}
		$("a[href*=\\#]").on("click", function (e) {
			if (this.pathname === window.location.pathname) {
				// Same page scroll
				e.preventDefault();
				smoothScrolling(this.hash, this);
			}
		});
	});
})(jQuery);

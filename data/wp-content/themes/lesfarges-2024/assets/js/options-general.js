(function ($) {
  var slogan2 = $("input#lfa_slogan_2").parents("td").html();
  var slogan2Tr = $("input#lfa_slogan_2").parents("tr");
  var sloganP = $("p#tagline-description");
  console.log($(slogan2));
  $(slogan2).insertBefore($(sloganP));
  $(slogan2Tr).remove();
})(jQuery);

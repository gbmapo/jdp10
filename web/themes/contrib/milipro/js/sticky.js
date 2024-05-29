jQuery(document).ready(function ($) {
  if ($(window).width() > 767) {
  $(window).scroll(function () {
    var headermainheight = jQuery('.header-main').outerHeight();
    if ($(this).scrollTop() >= headermainheight) {
      // var stikyheaderheight = jQuery('.header-main.sticky-header').outerHeight();
      $('.header-main').addClass('sticky-header animated fadeInDown');
      $('.sticky-header-height').css('padding-top', headermainheight);
    } else {
      $('.header-main').removeClass('sticky-header animated fadeInDown');
      $(".sticky-header-height").css('padding-top', '0');
    }
  });
  };
});

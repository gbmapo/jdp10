if ( jQuery('.slider-classic').length > 0 ) {
  var slider = tns({
    container: '.slider-classic',
    mode: "gallery",
    items: 1,
    slideBy: 'page',
    animateIn: 'fadeInUp',
    animateOut: 'fadeOutLeft',
    speed: 600,
    autoplay: true,
    controls: false,
    navPosition: "bottom",
    autoplayButtonOutput: false,
  });
}
if ( jQuery('.slider-layer').length > 0 ) {
  var slider = tns({
    container: '.slider-layer',
    mode: "gallery",
    items: 1,
    slideBy: 'page',
    animateIn: 'fadeInUp',
    animateOut: 'fadeOutLeft',
    speed: 600,
    autoplay: true,
    autoplayTimeout: 10000,
    controls: false,
    navPosition: "bottom",
    autoplayButtonOutput: false,
  });
}
if ( jQuery('.gallery-slider, .gallery-slider1, .gallery-slider2, .gallery-slider3, .gallery-slider4, .gallery-slider5').length > 0 ) {
  var slider = tns({
    container: '.gallery-slider, .gallery-slider1, .gallery-slider2, .gallery-slider3, .gallery-slider4, .gallery-slider5',
    mode: "gallery",
    items: 1,
    slideBy: 'page',
    animateIn: 'fadeInUp',
    animateOut: 'fadeOutLeft',
    speed: 600,
    autoplay: true,
    autoplayTimeout: 10000,
    controls: false,
    navPosition: "bottom",
    autoplayButtonOutput: false,
  });
}
if ( jQuery('.testimonials').length > 0 ) {
  var slider = tns({
    container: '.testimonials',
    items: 3,
    autoplay: true,
    nav: false,
    controlsPosition: 'bottom',
    autoplayButtonOutput: false,
    "responsive": {
      "300": {
        "items": 1
      },
      "500": {
        "items": 2
      },
      "768": {
        "items": 3
      }
    },
  });
}
if ( jQuery('.clients').length > 0 ) {
  var slider = tns({
    container: '.clients',
    items: 3,
    autoplay: true,
    nav: false,
    controlsPosition: 'bottom',
    autoplayButtonOutput: false,
    "responsive": {
      "300": {
        "items": 1
      },
      "500": {
        "items": 2
      },
      "768": {
        "items": 3
      }
    },
  });
}
/* Load jQuery.
------------------------------------------------*/
jQuery(document).ready(function ($) {
  // Mobile menu.
  $('.mobile-menu').click(function () {
    $(this).toggleClass('menu-icon-active');
    $(this).next('.primary-menu-wrapper').toggleClass('active-menu');
  });
  $('.close-mobile-menu').click(function () {
    $(this).closest('.primary-menu-wrapper').toggleClass('active-menu');
    $('.mobile-menu').removeClass('menu-icon-active');
  });

  // Full page search.
  $('.search-icon').on('click', function() {
    $('.search-box').addClass('open');
    return false;
  });
  $('.search-box-close, .search-close').on('click', function() {
    $('.search-box').removeClass('open');
    return false;
  });

  // Sliding sidebar.
  $('.sliding-panel-icon').click(function () {
    $('.sliding-sidebar').toggleClass('animated-panel-is-visible');
  });
  $('.close-animated-sidebar').click(function () {
    $('.sliding-sidebar').removeClass('animated-panel-is-visible');
  });

  // Elements -> Toggle.
  $(".toggle-content").hide();
	$(".toggle-title").click(function() {
		$(this).next('.toggle-content').slideToggle(300);
		$(this).toggleClass('active-toggle');
		return false;
  });

  // Elements -> Accordion.
  var accordion_head = $(".accordion-title"), accordion_para = $(".accordion-content"); // select heading and para and save as variable
	accordion_para.hide(); // hide all para
	accordion_head.click(function() {
		var current = $(this); // save the heading for easy referral
		if ( current.next(".accordion-content").is(":hidden") ) {
			current.siblings(".accordion-content").slideUp();
			current.next(".accordion-content").slideDown();
			current.siblings(".accordion-title").removeClass('active-accordion'); // remove active-accordion class from all siblings titles
			current.addClass("active-accordion"); // add class active-accordion to clicked title
		} else {
			current.next(".accordion-content").slideUp();
			current.removeClass("active-accordion"); // add class active-accordion to clicked title
		}
	});

  // Elements -> Tab.
  $(".tabs li:first-child").addClass('active-tab');
  $(".tab-content:first").addClass('active-tab-content');
  $(".tabs li a").click(function() {
  $(".tabs li").removeClass('active-tab');
  $(this).parent('li').addClass('active-tab');
});
  $(".tabs li a").click(function() {
    var t = $(this).attr('href');
    $('.tabs li a').addClass('inactive-tab');
    $(this).removeClass('inactive-tab');
    $('.tab-content').hide();
    $(t).fadeIn('slow');
    return false;
  });
  if($(this).hasClass('inactive-tab')) {
    $('.tabs li a').addClass('inactive-tab');
    $(this).removeClass('inactive-tab');
    $('.tab-content').hide();
    $(t).fadeIn('slow');
  }
  // Elements -> Popup.
  $(".popup-open").on("click", function() {
    $('.popup-content').removeClass("active");
    $(this).closest('.popup').find('.popup-content').addClass("active");
  });

  $(".popup-close").on("click", function(){
    $(this).closest('.popup-content').removeClass("active");
  });

  // Scroll To Top.
  $(window).scroll(function () {
    if ($(this).scrollTop() > 80) {
      $('.scrolltop').css('display', 'grid');
    } else {
      $('.scrolltop').fadeOut('slow');
    }
  });
  $('.scrolltop').click(function () {
    $('html, body').scrollTop(0);
  });

/* Function if device width is more than 767px.
------------------------------------------------*/
// Animated page elements.
if ($(window).width() > 767) {
  $('.animate-fadeIn, .animate-fadeInDown, .animate-fadeInLeft, .animate-fadeInRight, .animate-fadeInUp, .animate-zoomIn').css(
    'opacity','0'
  );
  $('.animate-bounce').viewportChecker({
    classToAdd: 'animated bounce',
    offset: 10
  });
  $('.animate-pulse').viewportChecker({
    classToAdd: 'animated pulse',
    offset: 100
  });
  $('.animate-zoomIn').viewportChecker({
    classToAdd: 'animated zoomIn',
    offset: 100
  });
  $('.animate-fadeIn').viewportChecker({
    classToAdd: 'animated fadeIn',
  });
  $('.animate-fadeInDown').viewportChecker({
    classToAdd: 'animated fadeInDown',
    offset: 100
  });
  $('.animate-fadeInLeft').viewportChecker({
    classToAdd: 'animated fadeInLeft',
    offset: 100
  });
  $('.animate-fadeInRight').viewportChecker({
    classToAdd: 'animated fadeInRight',
    offset: 100
  });
  $('.animate-fadeInUp').viewportChecker({
    classToAdd: 'animated fadeInUp',
    offset: 100
  });
}

// End document ready.
});

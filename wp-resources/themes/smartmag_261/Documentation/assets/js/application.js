// NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
// IT'S ALL JUST JUNK FOR OUR DOCS!
// ++++++++++++++++++++++++++++++++++++++++++

!function ($) {

  $(function(){

    var $window = $(window);

    // side bar
    /*setTimeout(function () {
      $('.bs-docs-sidenav').affix();
    }, 100);*/
    // make code pretty
    window.prettyPrint && prettyPrint();

  });

  add_offset = 55;
  
  if (window.location.href.indexOf("theme-sphere.com") == -1) {
    $('.about-online').show();
    $('.about-help').addClass('online-pad');

    //add_offset = 48;
  }

  $('.about-help .close').click(function() { 
    
    // adjust scrollspy
    $('body').removeClass('has-top');
    $('body').data()['bs.scrollspy'].options.offset = 0;

    $('.about-help').hide(); 
  });

  $('.img-polaroid').click(function() {
    $(this).toggleClass('enlarge');
  });

  $('body').scrollspy({ target: '.bs-docs-sidebar', offset: add_offset});

}(window.jQuery);
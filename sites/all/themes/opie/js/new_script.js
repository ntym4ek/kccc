/**
 * @file
 * Example of JavaScript file for theme.
 * You can edit it or write on your own.
 */
(function ($, Drupal, window, document, undefined) {

Drupal.behaviors.opie = {
  attach: function(context, settings) { 

    /* выпадающее меню */
    $('#fold-2055>a, #fold-272>a, #fold-2054>a').removeAttr("href");
    
    $('body').off('click', '#fold-2055>a, #fold-272>a, #fold-2054>a'); // без этого click срабатывает 9 раз вместо одного
    $('body').on('click', '#fold-2055>a, #fold-272>a, #fold-2054>a', function () {   
      // закрыть, если другие открыты
      $('.foldout').each( function (){ 
        if ( $(this).data( 'expanded' )) { 
          $(this).data('expanded', false); 
          parent = $(this).parent();
          $('a', parent).removeClass('folded');
          $(this).slideUp(200);
        }
      });
      
      // открыть
      var parent = $(this).parent();
      if ( !$('.foldout', parent).data('expanded')) {  
        $(this).addClass('folded');
        doc_w = $(document).width();
        doc_h = $(document).height();
        if (!$('div').is('#modal')) $('body').append('<div id="modal" style="width: '+doc_w+'px; height:'+doc_h+'"></div>');         
        $('#modal').animate({ opacity: '0.6', easing: "swing" }, 500, function() {
          $('.foldout', parent).slideDown(450);      
        });
        $('.foldout', parent).data('expanded', true);    
      }  
      
      $('#modal, .foldout-close').click(function () {     
        $('.foldout').each( function (){ 
          if ( $(this).data('expanded' )) win = $(this); });
        
        $( win ).slideUp(200, function() { 
          $('#modal').animate({ opacity: '0', easing: "swing" }, 500, function() { 
            $(this).remove(); 
          }); 
        });
        $(win).data('expanded', false); 
        parent = $(win).parent();
        $('a', parent).removeClass('folded');
      });     
    });	
    
    /* hover новостей на главной */
    $('.text-slide').mouseenter(function(){
      $( '.text-wrap', this ).slideToggle(150);
      $( '.submit-info', this ).animate( {'height':'100%'}, 100);
      $( 'h2.transparent', this ).fadeToggle(150);
    }).mouseleave(function(){
      $( '.text-wrap', this  ).slideToggle(150);
      $( 'h2.transparent', this ).fadeToggle(50);
      $( '.submit-info', this ).animate( {'height':'60'}, 100);
    });    
    
    /* hover категорий каталога */
    $('.category.teaser').on('mouseenter', function(){
      $(this).parent().find( '.description' ).stop(true, true).slideToggle(250);
    });
    $('.category.teaser').on('mouseleave', function(){
      $(this).parent().find( '.description' ).stop(true, true).slideToggle(150);
    });  

    /* hover submit-info в списке новостей */
    $('.news.teaser .img-wrap').mouseenter(function(){
      $( '.submit-info', this ).animate( {'height':'100%'}, 100);
    }).mouseleave(function(){
      $( '.submit-info', this ).animate( {'height':'60'}, 100);
    });    
    
    /* плавающий блок navigation */
    $(window).scroll(function() {
        var top = $(document).scrollTop();
        var offset = parseInt( $("body").css('padding-top'));
        if ( offset != NaN ) offset = offset + 10;
        if (top < 130) {
          $("#navigation").css({ top: '0', position: 'relative' });
          $(".navigation-place").css({display: 'none'});
        } else {
          $("#navigation").css({top: offset+'px', position: 'fixed'});
          $(".navigation-place").css({display: 'block'});
        }
    });    
  }
};
  
})(jQuery, Drupal, this, this.document);


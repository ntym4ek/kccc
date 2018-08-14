setTimeout( function() {
	var nextBG = jQuery( '.views-field-field-promo-image img', nextSlideElement ).attr('src');
	var currBG = jQuery( '.views-field-field-promo-image img', currSlideElement ).attr('src');
	
	if( !jQuery('div').is('#bg')) {
		var w = jQuery(document).width();
		jQuery('body').append('<div id="bg"></div>');
	}
		
	jQuery('body').css('background-image', 'url(' + currBG + ')');
	jQuery('#bg').css('opacity', 0);
	jQuery('#bg').css('background-image', 'url(' + nextBG + ')');	
	jQuery('#bg').animate( { opacity: 1 }, 800);
	
	var nextIndex = jQuery( nextSlideElement ).index();
	var currIndex = jQuery( currSlideElement ).index();
	jQuery( '#views_slideshow_pager_field_item_bottom_main_slider-block_'+currIndex ).animate( { 'opacity': '0.4' } );
	jQuery( '#views_slideshow_pager_field_item_bottom_main_slider-block_'+nextIndex ).animate( { 'opacity':'1' } );
	
}, 100);
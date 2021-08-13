
/**
 * @file
 * Attaches the behaviors for the Bootstrap Slider module.
 */

(function ($) {

  Drupal.behaviors.carousel = {
    attach: function (context, settings) {
			$('#bootstrap-slider').fadeIn('slow');
      // Initialize the slider
      $('#bootstrap-slider').carousel({
        'interval': Drupal.settings.bootstrap_slider.effectTime
      });
    }
  };
}(jQuery));

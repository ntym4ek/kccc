<?php

/**
 * @file
 * Theme and preprocess functions for Bootsrap Slider.
 */

/**
 * Implements template_preprocess_hook().
 */
function template_preprocess_bootstrap_slider_slider(&$variables) {
  $variables['slides'] = bootstrap_slider_get_slides(FALSE);
}
<?php
/**
 * @file
 * Generate configuration form and save settings.
 */

/**
 * Configuration form for slider options.
 */
function bootstrap_slider_option_configuration_form($form, &$form_state) { 
  
	$form['mtm_settings'] = array(
		'#type' => 'fieldset',
		'#title' => t('Bootstrap Slider Options'),
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,
	);

  $form['mtm_settings']['bb_slideshow_effect'] = array(
    '#type' => 'select',
    '#title' => t('Effect'),
    '#options' => array(
      'fade' => t('Fade'),
      'slide' => t('Slide'),
    ),
    '#default_value' => variable_get('bb_slideshow_effect', 'slide'),
    '#description' => t('Select an effect. The chosen effect will be used to transition between slides.'),
  );

  $form['mtm_settings']['bb_slideshow_effect_time'] = array(
    '#type' => 'textfield',
    '#title' => t('Effect duration'),
    '#maxlength' => 6,
    '#size' => 6,    
    '#default_value' => variable_get('bb_slideshow_effect_time','5000'),
    '#description' => t('Enter a time in milliseconds. The amount of time to delay between automatically cycling an item.'),
    '#field_suffix' => t('ms'),
  );

  return system_settings_form($form);
}
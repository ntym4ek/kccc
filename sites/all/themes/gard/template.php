<?php
/**
 * @file
 * The primary PHP file for this theme.
 */

/**
 * Implements hook_pre_render().
 */
function gard_pre_render($element) {
    // добавить для form select обёртку div, чтобы можно было темизировать с помощью css
    if (in_array($element['#type'], array('select', 'textfield'))) {
        $element['#field_prefix'] = '<div class="form-input-wrapper">';
        $element['#field_suffix'] = '</div>';
    }

    return $element;
}
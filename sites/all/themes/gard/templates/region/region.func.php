<?php
/**
 * @file
 * Stub file for "region" theme functions.
 */

/**
 * Returns HTML for a region.
 */
function gard_region__no_wrapper(&$variables) {
    $elements = $variables['elements'];

    return $elements['#children'];
}
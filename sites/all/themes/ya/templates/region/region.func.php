<?php
/**
 * @file
 * Stub file for "region" theme functions.
 */

/**
 * Returns HTML for a region.
 */
function ya_region__no_wrapper(&$vars) {
    $elements = $vars['elements'];

    return $elements['#children'];
}

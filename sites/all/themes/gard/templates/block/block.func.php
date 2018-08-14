<?php
/**
 * @file
 * Stub file for block theme functions.
 */

/**
* Returns HTML for a block.
*
*/
function gard_block__no_wrapper(&$variables) {
    $elements = $variables['elements'];

    return $elements['#children'];
}

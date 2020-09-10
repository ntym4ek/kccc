<?php
/**
 * @file
 * Stub file for block theme functions.
 */

/**
* Returns HTML for a block.
*
*/
function ya_block__no_wrapper(&$vars) {
    $elements = $vars['elements'];

    return $elements['#children'];
}

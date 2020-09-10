<?php

/**
 * @file
 * Stub file for "region" theme hook [pre]process functions.
 */

/**
 * Pre-processes variables for the "region" theme hook.
 *
 * See template for list of available variables.
 *
 * @param array $vars
 *   An associative array of variables, passed by reference.
 *
 * @see region.tpl.php
 *
 * @ingroup theme_preprocess
 */
function ya_preprocess_region(array &$vars)
{
  $vars['theme_hook_suggestions'][] = 'region__no_wrapper';
}

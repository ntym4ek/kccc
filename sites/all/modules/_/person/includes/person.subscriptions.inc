<?php
/**
 * Created by PhpStorm.
 * User: Михаил
 * Date: 25.08.2016
 * Time: 11:58
 */

/**
 * функция,
 */
function person_subscriptions($acid)
{
    $output = false;
    $output .= views_embed_view('user_subscribes', 'page');

    return $output;
}

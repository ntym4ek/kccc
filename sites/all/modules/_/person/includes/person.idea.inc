<?php
/**
 * Created by PhpStorm.
 * User: Михаил
 * Date: 25.08.2016
 * Time: 11:58
 */

/**
 * функция, возвращающая контент страницы Идей
 */
function person_idea($acid)
{
    global $user;
    
    $output = false;

    // вернуть список только, если свой аккаунт
    if ($user->uid == $acid) {
        $output = views_embed_view('webform_submissions_account', 'page_1', $acid);
    }

    return $output;
}

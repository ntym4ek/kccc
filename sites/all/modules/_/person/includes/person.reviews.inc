<?php
/**
 * Created by PhpStorm.
 * User: Михаил
 * Date: 25.08.2016
 * Time: 11:58
 */

/**
 * функция, возвращающая контент страницы Отзывы
 */
function person_reviews($acid) {
    global $user;
    
    delBubbles(array('uid' => $user->uid, 'section' => BS_REVIEWS));

    $output = false;

    // вернуть список только, если свой аккаунт 
    if ($user->uid == $acid) {
        $output = views_embed_view('reviews', 'page_4', $acid);
    }

    return $output;
}

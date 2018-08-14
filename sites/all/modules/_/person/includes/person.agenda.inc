<?php

/**
 * функция, возвращающая контент страницы Афиша
 */
function person_agenda($acid) {
    global $user;
    
    $output = false;

    // вернуть список только, если свой аккаунт
    if ($user->uid == $acid) {
        $output = views_embed_view('agenda', 'page_2', $acid);
    }

    return $output;
}

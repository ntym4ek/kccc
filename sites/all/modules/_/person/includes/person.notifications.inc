<?php
/**
 * Created by PhpStorm.
 * User: Михаил
 * Date: 25.08.2016
 * Time: 11:58
 */

/**
 * функция, возвращающая контент страницы Уведомления
 */
function person_notifications($acid)
{
    global $user;
    
    $output = false;

    // вернуть список только, если свой аккаунт
    if ($user->uid == $acid) {
        // получить id Типов сообщений, на которые подписан пользователь
        $subscripted_notification_ids = ext_message_get_user_subscription_notification_ids($acid);

        // определить фильтр, если установлен
        $status = '';
        if (!isset($_GET['status']) || strtolower($_GET['status']) == 'new') {
            $status = 0;
        }

        // сформировать вывод
        $output =   '<div class="notifications">' .
                        '<div class="n-controls">' .
                            '<div class="n-filter">' .
                                '<a href="/person/' . $acid . '/notifications?status=new" ' . ($status === 0 ? 'class="active" ' : '') . '>' . t('Only new') . '</a>' .
                                '<a href="/person/' . $acid . '/notifications?status=all" ' . ($status === '' ? 'class="active" ' : '') . '>' . t('All notifications') . '</a>' .
                            '</div>' .
                            '<div class="n-read-all">' .
                                '<a href="/message/all/got?back=' .$_GET['q'] . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . '" title="' . t('Set all notification read') . '" class="n-all-got"><i class="fas fa-check-double"></i></a>' .
                            '</div>' .
                        '</div>' .
                    '</div>';

        // списко Уведомлений по полученным Типам сообщений
        $output .= views_embed_view('user_notifications', 'page', implode('+', $subscripted_notification_ids), $status);
    }

    return $output;
}

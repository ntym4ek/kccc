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
function person_notifications($acid) {
    global $user;
    
    $output = false;

    // вернуть список только, если свой аккаунт
    if ($user->uid == $acid) {
        $subscripted_notification_ids = [];

        // если есть флаги, относящиеся к Подпискам
        if (module_exists('message_subscribe') && $flags = message_subscribe_flag_get_flags('taxonomy_term')) {

            $fids = array();
            foreach ($flags as $flag) {
                $fids[$flag->fid] = $flag->name;
            }

            $query = db_select('flagging', 'fc');
            $query->condition('entity_type', 'taxonomy_term');
            $query->condition('fid', array_keys($fids), 'IN');
            $query->condition('fc.uid', $acid);
            $query->addField('fc', 'entity_id');
            if ($subscription_tids = $query->execute()->fetchCol()) {
                $query = db_select('field_data_field_message_type_ref', 'mtr');
                $query->condition('mtr.entity_id', $subscription_tids, 'IN');
                $query->addField('mtr', 'field_message_type_ref_target_id');
                $subscripted_notification_ids = $query->execute()->fetchCol();
            }
        }

        // аргумент Статус
        $status = '';
        if (isset($_GET['status'])) {
            if (strtolower($_GET['status']) == 'new') $status = 0;
        }

        $output =   '<div class="notifications">' .
                        '<div class="n-controls">' .
                            '<div class="n-filter">' .
                                '<a href="/person/' . $acid . '/notifications?status=all" ' . ($status === '' ? 'class="active" ' : '') . '>' . t('All notifications') . '</a>' .
                                '<a href="/person/' . $acid . '/notifications?status=new" ' . ($status === 0 ? 'class="active" ' : '') . '>' . t('Only new') . '</a>' .
                            '</div>' .
                            '<div class="n-read-all">' .
                                '<a href="/message/all/got?back=' .$_GET['q'] . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . '" title="' . t('Set all notification read') . '" class="n-all-got"><i class="fas fa-check-double"></i></a>' .
                            '</div>' .
                        '</div>' .
                    '</div>';
        $output .= views_embed_view('user_notifications', 'page', implode('+', $subscripted_notification_ids), $status);
    }

    return $output;
}

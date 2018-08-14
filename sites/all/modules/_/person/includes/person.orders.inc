<?php
/**
 * Created by PhpStorm.
 * User: Михаил
 * Date: 25.08.2016
 * Time: 11:58
 */

/**
 * функция, возвращающая контент страницы Блоги
 */
function person_orders($acid, $op, $oid = '') {
    global $user;
    $output = false;
    if ($op == null) $op = 'list';
        
    // вернуть список только, если свой аккаунт 
    if ($user->uid == $acid) {
        switch ($op) {
            case 'list':
                $output = views_embed_view('commerce_user_orders', 'order_page', $acid);
                break;
            case 'view':
                $commerce_order = commerce_order_load($oid);
                $output = render(entity_view('commerce_order', array(entity_id('commerce_order', $commerce_order) => $commerce_order)));
                break;
        }
    }
    
    return $output;
}

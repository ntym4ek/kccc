<?php

define ('FEE' , .9989999);
define ('BTC_FEE' , .997999);

define ('CNY_MIN_AMOUNT' , 10);
define ('MIN_PROFIT' , 1.005);
define ('MIN_VOLUME' , 1000000);
define ('MAX_OPEN_TRADES' , 15);

define ('ORDER_BUY' , 1);
define ('ORDER_SELL' , 2);

define ('STATUS_OPEN' , 1);
define ('STATUS_CLOSED' , 2);

define ('MSG_OPEN' , 1);
define ('MSG_PROFIT_UP' , 2);
define ('MSG_PROFIT_DOWN' , 3);


/**
 * Реализация hook_entity_info()
 */
function bot_entity_info() {
    return array(
        'trade' => array(                                      // Системное имя сущности
            'label' => t('Trade'),                              // Человеко-понятное имя сущности
            'entity class' => 'Entity',                         // Класс сущности
            'controller class' => 'EntityAPIController',        // Контроллер сущности
            'base table' => 'trades',                          // Таблица, в которой хранится информация об альбомах
            'entity keys' => array(
                'id' => 'trade_id',                                  // Название поля, в котором располагаются идентификаторы альбомов
            ),
            'module' => 'bot',
        ),
    );
}


function bot_page_38($mode = 'nojs')
{
    $GLOBALS['btc_fee_array'] = array('btc', 'ltc', 'tmc');

    drupal_add_library('system', 'drupal.ajax');
    drupal_add_js('misc/ajax.js');
    drupal_add_js(drupal_get_path('module', 'bot') . '/js/bot.btc38.js');
    drupal_add_css(drupal_get_path('module', 'bot') . '/css/bot.css');

    $data = array();

    // получить все пары
    $result = drupal_http_request('http://api.btc38.com/v1/ticker.php?c=all');
    if ($result->status_message == 'OK') {
        $data['markets'] = drupal_json_decode($result->data);
    }

    // получить все монеты, по которым уже открыты ордера
    $data['coins_in_trade'] = db_get_coins_in_trade();

    // провести анализ профитности
    _make_analisys_38($data);

    // проверить актуальность открытых сделок
    _check_open_orders($data);

    // повесить новые ордера
    //_start_trade($data);

    // вывод результатов
    $data['open_trades'] = db_get_open_trades();
    $content['open_trades'] = theme('bot_open_trades_table', array('data' => $data));

    $closed_trades = db_get_closed_trades();
    $content['closed_trades'] = theme('bot_closed_trades_table', array('trades' => $closed_trades));

    $content['btc38'] = theme('bot_table_btc38', array('data' => $data));
    $content['messages'] = '<div id="bot-messages"><p>'. implode('<br />', isset($GLOBALS['messages']) ? $GLOBALS['messages'] : array()) . '</p></div>';

    $output = theme('bot_page', array('content' => $content));


    if ($mode == 'ajax') {
        $commands[] = ajax_command_invoke('#open-trades', 'replaceWith', array($content['open_trades']));
        $commands[] = ajax_command_invoke('#closed-trades', 'replaceWith', array($content['closed_trades']));
        $commands[] = ajax_command_invoke('#btc38', 'replaceWith', array($content['btc38']));
        $commands[] = ajax_command_invoke('#bot-messages', 'replaceWith', array($content['messages']));
        print ajax_render($commands);  drupal_exit();
    } else {
        return $output;
    }
}

/**
 * создать ордер на покупку по текущему профиту
 */
function _start_trade(&$data)
{
    $counter = 0;
    foreach($data['pairs'] as $market) {
        // получить цену последней BUY сделки по монете
        $last_price = 0;
        if (!empty($data['coins_in_trade'])) {
            foreach ($data['coins_in_trade'] as $coin_in_trade) {
                if ($coin_in_trade['coin'] == $market['name']) $last_price = $coin_in_trade['price'];
            }
        }
        $top = $last_price * 1.03;
        $low = $last_price * 0.97;

        // объём рынка в CNY
        $volumeCNY = $market['vol'] * $market['sell'];

        // поставить ордер на монету соответствующую условиям
        if ((!$last_price || ($market['buy'] < $low || $market['buy'] > $top)) // по ней нет ордера или есть, но рынок ушел на 3% вверх или вниз
            && count($data['coins_in_trade']) + $counter < MAX_OPEN_TRADES      // сделок меньше максимально допустимого
            && $volumeCNY > MIN_VOLUME                                          // объёмы не меньше требуемого
            && $market['profit'] > MIN_PROFIT)
        {
            $req = array(
                'type' => ORDER_BUY,
                'mk_type' => 'CNY',
                'price' => $market['buy'] + $market['frac'],
                'amount' => number_format(CNY_MIN_AMOUNT / $market['buy'], 6, '.', ''),
                'coinname' => $market['name'],
            );
            if ($result = _make_order($req)) {
                $data['messages'][$result['trade_id']] = MSG_OPEN;
                $counter++;
            }
        }
    }
}






function _make_order($req)
{
    $result = api_query_38('submitOrder', $req);

    if (explode('|', $result)[0] == 'succ') {
        $order_id = empty(explode('|', $result)[1]) ? 0 : explode('|', $result)[1];

        // количество после покупки станет меньше, а при продаже надо продавать всё что куплено
        $fee = in_array($req['coinname'], $GLOBALS['btc_fee_array']) ? BTC_FEE : FEE;
        $amount = ($req['type'] == ORDER_BUY) ? $req['amount'] * $fee : $req['amount'];

        $status = STATUS_OPEN;
        if (!$order_id && $req['type'] == ORDER_SELL) $status = STATUS_CLOSED;

        $trade = entity_create('trade', array('order_id' => $order_id, 'type' => $req['type'], 'amount' => $amount, 'price' => $req['price'],
            'coin' => $req['coinname'], 'basecoin' => $req['mk_type'], 'created' => REQUEST_TIME, 'status' => $status));
        $trade->save();

        return array(
            'trade_id' => $trade->trade_id,
            'order_id' => $order_id
        );
    } else $GLOBALS['messages'][] = $result;

    return false;
}
function _cancelOrder($req)
{
    // отменить ордер
    $result = api_query_38('cancelOrder', $req);

    // удалить из БД
    if ($result == 'succ') {
        db_delete_order_by_OID($req['order_id']);
        return true;
    } else $GLOBALS['messages'][] = $result;

    return false;
}
function _move_order($order_id, $coin, $type, $price, $amount)
{
    // отменить BUY ордер
    $req = array(
        'order_id' => $order_id,
        'mk_type' => 'CNY',
        'coinname' => $coin
    );

    if (_cancelOrder($req)) {
        // новый BUY ордер
        $req = array(
            'type' => $type,
            'mk_type' => 'CNY',
            'price' => $price,
            'amount' => number_format($amount, 6, '.', ''),
            'coinname' => $coin
        );
        return _make_order($req);
    }

    return false;
}


function _make_analisys_38(&$data)
{
    // базовая валюта CNY
    $pairs = array();
    $markets = $data['markets'];

    foreach($markets as $key => $market) {
        if (!empty($market['ticker'])) {
            $buy = $market['ticker']['buy'];
            $sell = $market['ticker']['sell'];

            // вычислить минимальную долю цены
            $num = strlen($sell) > strlen($buy) ? $sell : $buy;
            $num = strlen($market['ticker']['high']) > strlen($num) ? $market['ticker']['high'] : $num;
            $num = strlen($market['ticker']['low']) > strlen($num) ? $market['ticker']['low'] : $num;
            $depth = strlen(empty(explode('.',$num)[1]) ? 0 : explode('.',$num)[1]);
            $frac = .1;
            while(--$depth > 0) {
                $frac = $frac / 10;
            }
            $market['ticker']['frac'] = $frac;

            // вычислить профит с учетом закупки и продажи по цене со смещением на $frac
            $fee = in_array($key, $GLOBALS['btc_fee_array']) ? BTC_FEE : FEE;
            $buy_coins = 1 / ($buy + $frac) * $fee;
            $profit = $buy_coins * ($sell - $frac) * $fee;

            $market['ticker']['name'] = $key;
            $market['ticker']['volCNY'] = $market['ticker']['vol'] * $buy;
            $market['ticker']['profit'] = $profit;


            $data['markets'][$key] = $market['ticker'];
            $pairs[$profit * 100000] = $market['ticker'];
        }
    }

    krsort($pairs);
    $data['pairs'] = $pairs;
}

function _check_open_orders(&$data)
{
    reset($data['pairs']);
    // все активные сделки из БД
    $db_orders = db_get_open_orders();

    foreach($db_orders as $db_order) {
        // получим висящие ордера
        $orders = array();
        $result = api_query_38('getOrderList', array('mk_type' => 'CNY', 'coinname' => $db_order->coin));
        if ($result = drupal_json_decode($result)) {
            foreach ($result as $res) $orders[$res['id']] = $res;
        }

        $market = $data['markets'][$db_order->coin];
        if (!$market['name'] || !$market['frac'] || !$market['buy'] || !$market['sell'] || !$market['profit']) continue;

        // если ордер по сделке ещё висит на BTC38
        if (isset($orders[$db_order->order_id])) {
            // проверить профитность, текущую цену и, если нужно, перенести сделку или отменить
            if ($db_order->type == ORDER_BUY) {

                // если профитность меньше порога, отменить ордер
                if ($market['profit'] < MIN_PROFIT) {
                    $req = array(
                        'order_id' => $db_order->order_id,
                        'mk_type' => 'CNY',
                        'coinname' => $db_order->coin
                    );
                    if (_cancelOrder($req)) $GLOBALS['messages'][] = 'Заказ BUY на ' . $market['name'] . ' отменен из-за низкого профита.';
                    continue;
                }

                // если цена рынка изменилась в бОльшую сторону - перенести ордер
                if ($market['buy'] > (float)$db_order->price) {
                    $new_price = $market['buy'] + $market['frac'];
                    $result = _move_order($db_order->order_id, $db_order->coin, ORDER_BUY, $new_price, CNY_MIN_AMOUNT / $new_price);
                    if ($result) $data['messages'][$result['trade_id']] = MSG_PROFIT_DOWN;
                }
                // если цена рынка изменилась в меньшую сторону - перенести ордер
                else {
                    // получить стакан рынка
                    $result = drupal_http_request('http://api.btc38.com/v1/depth.php?mk_type=cny&c=' . $db_order->coin);
                    if ($result->status_message == 'OK') {
                        $depth = drupal_json_decode($result->data);

                        // если цена предыдущего ордера ниже - подтянуть свой к ней
                        $new_price = $depth['bids'][1][0] + $market['frac'];
                        if ($new_price < (float)$db_order->price) {
                            $result = _move_order($db_order->order_id, $db_order->coin, ORDER_BUY, $new_price, CNY_MIN_AMOUNT / $new_price);
                            if ($result) $data['messages'][$result['trade_id']] = MSG_PROFIT_UP;
                        }
                    }
                }
            }

            // если висит ордер SELL
            else {
                // проверить профитность, текущую цену и, если нужно, перенести сделку
                $entities = entity_load('trade', array($db_order->buy_order_id));
                if ($entities) {
                    $db_order_buy = $entities[$db_order->buy_order_id];

                    // найти минимальную профитную цену
                    $fee = in_array($db_order->coin, $GLOBALS['btc_fee_array']) ? BTC_FEE : FEE;
                    $min_sell_price = 1.004 / ((1 / $db_order_buy->price * $fee) * $fee);

                    // продавать не ниже цены с минимальным профитом
                    $sell_price = ($market['sell'] - $market['frac']) < $min_sell_price ? $min_sell_price : ($market['sell'] - $market['frac']);
                    // отформатировать цену по ценам сделки
                    $dec = strlen(explode('.', $market['frac'])[1]);
                    $sell_price = number_format($sell_price, $dec, '.', '');

                    // если цена рынка изменилась в меньшую сторону - перенести ордер
                    if ($sell_price < (float)$db_order->price) {
                        if ($result = _move_order($db_order->order_id, $db_order->coin, ORDER_SELL, $sell_price, $db_order->amount)) {
                            db_order_update($result['trade_id'], array('buy_order_id' => $db_order->buy_order_id));
                            $data['messages'][$result['trade_id']] = MSG_PROFIT_DOWN;
                        }
                    }
                    // если цена рынка изменилась в бОльшую сторону - перенести ордер
                    else {
                        // получить стакан рынка
                        $reslt = drupal_http_request('http://api.btc38.com/v1/depth.php?mk_type=cny&c=' . $db_order->coin);
                        if ($reslt->status_message == 'OK') {
                            if ($depth = drupal_json_decode($reslt->data)) {
                                // если цена предыдущего ордера выше - подтянуть свой к ней
                                $new_price = $depth['asks'][1][0] - $market['frac'];
                                if ($new_price > (float)$db_order->price) {
                                    if ($result = _move_order($db_order->order_id, $db_order->coin, ORDER_SELL, $new_price, $db_order->amount)) {
                                        db_order_update($result['trade_id'], array('buy_order_id' => $db_order->buy_order_id));
                                        $data['messages'][$result['trade_id']] = MSG_PROFIT_UP;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //$orders = drupal_json_decode($result);

        }
        // если ордер исполнен
        else {
            if ($db_order->type == ORDER_BUY) {
                // проверить профитность
                // найти минимальную профитную цену
                $fee = in_array($db_order->coin, $GLOBALS['btc_fee_array']) ? BTC_FEE : FEE;
                $min_sell_price = MIN_PROFIT / ((1 / $db_order->price * $fee) * $fee);

                // продавать не ниже цены с минимальным профитом
                $sell_price = ($market['sell'] - $market['frac']) < $min_sell_price ? $min_sell_price : ($market['sell'] - $market['frac']);
                // отформатировать цену по ценам сделки
                $dec = strlen(explode('.', $market['frac'])[1]);
                $sell_price = number_format($sell_price, $dec, '.', '');

                // запросить баланс
                //if ($result = api_query_38('getMyBalance')) $balances = drupal_json_decode($result);

                // создать ордер на продажу
                $req = array(
                    'type' => ORDER_SELL,
                    'mk_type' => 'CNY',
                    'price' => $sell_price,
                    'amount' => number_format($db_order->amount, 6, '.', ''),
                    'coinname' => $db_order->coin,
                );
                if ($result = _make_order($req)) {
                    $data['messages'][$result['trade_id']] = MSG_OPEN;

                    // записать в сделку SELL информацию о сделке BUY
                    db_order_update($result['trade_id'], array('buy_order_id' => $db_order->trade_id));

                    // обновить статус сделки BUY на STATUS_CLOSED
                    db_order_update($db_order->trade_id, array('status' => STATUS_CLOSED));
                }
            }
            // если завершена сделка SELL
            else {
                db_order_update($db_order->trade_id, array('status' => STATUS_CLOSED, 'created' => REQUEST_TIME));
                $GLOBALS['messages'][] = 'Сделка для ' . $market['name'] . ' завершена';
            }
        }
    }
}




/**
 * функции для работы с БД
 */
function db_get_open_orders()
{
    $result = entity_load('trade', false, array('basecoin' => 'CNY', 'status' => STATUS_OPEN));

    return $result;
}

// возвращает монеты, по которым открыты сделки
function db_get_coins_in_trade()
{
    $trades = array();

    if ($result = entity_load('trade', false, array('basecoin' => 'CNY', 'status' => STATUS_OPEN))) {
        foreach($result as $id => $trade) {
            $trades[$trade->trade_id] = array(
                'coin' => $trade->coin,
                'price' => $trade->price,
            );
        }
    }

    return $trades;
}

function db_delete_order_by_OID($order_id)
{
    $trade_ids = array();

    $result = entity_load('trade', false, array('order_id' => $order_id));
    foreach($result as $item) $trade_ids[] = $item->trade_id;
    entity_delete_multiple('trade', $trade_ids);
}

function db_order_update($trade_id, $data)
{
    if ($item = entity_load('trade', array($trade_id))) {
        foreach($item as $entity) {
            foreach ($data as $field => $value) {
                $entity->{$field} = $value;
            }
            $entity->save();
        }
    }
}

function db_get_open_trades()
{
    $trades = array();
    if ($result = entity_load('trade', false, array('status' => STATUS_OPEN))) {
        foreach($result as $tid => $trade) {
            if ($trade->type == ORDER_BUY) {
                $trades[$tid] = array(
                    'tid' => $tid,
                    'name' => $trade->coin,
                    'buy' => $trade->price,
                    'amount' => $trade->amount,
                    'type' => 'BUY',
                    'created' => $trade->created,
                );
            } else {
                $buy_trade = entity_load('trade', array($trade->buy_order_id));
                $trades[$tid] = array(
                    'tid' => $tid,
                    'name' => $trade->coin,
                    'buy' => $buy_trade[$trade->buy_order_id]->price,
                    'sell' => $trade->price,
                    'amount' => $trade->amount,
                    'type' => 'SELL',
                    'created' => $trade->created,
                );

            }
        }
    }

    return $trades;
}
function db_get_closed_trades()
{
    $tids = $trades = array();

    // выбрать последние 10 SELL сделок
    $query = db_select('trades', 't');
    $query->addField('t', 'trade_id');
    $query->addField('t', 'buy_order_id');
    $query->condition('t.status', STATUS_CLOSED);
    $query->condition('t.type', ORDER_SELL);
    $query->orderBy('t.created', 'DESC');
    $query->range(0, 10);
    if ($result = $query->execute()->fetchAll()) {
        // собрать trade_id SELL сделок и их BUY сделок
        foreach($result as $item) {
            $tids[] = $item->trade_id;
            $tids[] = $item->buy_order_id;
        }
    }

    if ($tids) {
        $entities = entity_load('trade', $tids);
        foreach($entities as $tid => $entity) {
            if ($entity->type == ORDER_SELL) {
                $sell_price = $entity->price;
                $buy_price = $entities[$entity->buy_order_id]->price;

                $fee = in_array($entity->coin, $GLOBALS['btc_fee_array']) ? BTC_FEE : FEE;
                $earnings = $sell_price * $entity->amount * $fee;

                $trades[$tid] = array(
                    'name' => $entity->coin,
                    'sell' => $sell_price,
                    'buy' => $buy_price,
                    'created' => $entity->created,
                    'profit' => $earnings - CNY_MIN_AMOUNT,
                );
            }
        }
    }
    return $trades;
}

function api_query_38($method, array $data = array())
{
    $API_KEY = '578fb3bead17d449e908e09cc115ee81';
    $API_SEC = '82bb497e777154a20446a8b81d3ed988cfdc4ed7284b56efa19e96ade3e0d24b';
    $USER_ID = 321259;

    $date = new DateTime();
    $stamp = $date->getTimestamp();
    $md5 = $API_KEY . '_' . $USER_ID . '_' . $API_SEC . '_' . $stamp;
    $md5 = md5($md5);
    $post = array("key" => $API_KEY, "skey" => $API_SEC, "time" => $stamp, "md5" => $md5);
    $post = array_merge($post, $data);

    $ch = curl_init ();
    curl_setopt($ch, CURLOPT_URL, 'http://api.btc38.com/v1/' . $method . '.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; btc38 PHP bot; ' . php_uname('a') . '; PHP/' . phpversion() . ')');
    $res = curl_exec($ch);

    //if ($res === false) throw new Exception('Could not get reply: ' . curl_error($ch));

    return $res;
}
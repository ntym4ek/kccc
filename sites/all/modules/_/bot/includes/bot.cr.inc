<?php

function bot_page_cr()
{
    $output = '';

    drupal_add_library('system', 'drupal.ajax');

    // получить все рынки
    $markets = drupal_json_decode(api_query_cr("GetMarkets", array('Market'=> '')));

    // получить все монеты
    $pairs = drupal_json_decode(api_query_cr("GetTradePairs", array()));

    if ($markets['Success'] && $pairs['Success']) {
        $data['markets'] = $markets['Data'];

        // переиндексировать по именам
        foreach ($pairs['Data'] as $pair) {
            $data['pairs'][$pair['Label']] = $pair;
        }

        // составить 3x цепочки
        _set_chains_cr($data);

        // провести анализ
        _make_analisys_cr($data);

        // получить объёмы профитных цепочек
        _get_volumes_cr($data);

        // определить доступные для профитной торговли объёмы
        _set_profit_volumes_cr($data);

        // выполнить цепочку сделок
        _make_trade_cr($data);

        // баланс
//    $balance = drupal_json_decode(api_query("GetBalance", array( 'CurrencyId'=> 1 )));
//    $chains['balanceBTC'] = $balance ? $balance['Data'][0]['Total'] : 'недоступен';

        // открытые ордера
//    $orders = drupal_json_decode(api_query("GetOpenOrders", array('Market'=> '')));
//    $data['my_orders'] = $orders['Data'];


        // вывести результат в таблицу
        $output = theme('bot_table', array('data' => $data));
    }

    return $output;
}

function _make_trade_cr(&$data)
{
    reset($data['chains']);
    while($chain = current($data['chains'])) {
        $volumeBTC = isset($chain['analisys']['volumeBTC']) ? $chain['analisys']['volumeBTC'] : 0;
        if ($volumeBTC > .00005) $volumeBTC = .00006; else return;

        return;
        if ($chain['analisys']['fwd'] > 1 || $chain['analisys']['bwd'] > 1) {
            $market0 = $data['markets'][$chain['markets'][0]];
            $market1 = $data['markets'][$chain['markets'][1]];
            $market2 = $data['markets'][$chain['markets'][2]];

            // прямая цепочка
            if ($chain['analisys']['fwd'] > $chain['analisys']['bwd']) {

                // купить
                $amount = $volumeBTC / $market0['AskPrice'];
                $order = drupal_json_decode(api_query_cr("SubmitTrade", array(
                    'TradePairId' => $chain['markets'][0],
                    'Type' => 'Buy',
                    'Rate' => $market0['AskPrice'],
                    'Amount' => $amount
                )));
                // если неудачно - не продолжать
                if (!$order['Success']) return;
                // если ордер не выполнен сразу - отменить его
                if (!empty($order['Data']['OrderId'])) {
                    do {
                        $cancelOrder = drupal_json_decode(api_query_cr("CancelTrade", array(
                            'Type' => 'Trade',
                            'OrderId' => $order['Data']['OrderId'],
                        )));
                    } while ($cancelOrder['Success']);
                }

                // продать
                $amount = $volumeBTC / $market2['BidPrice'] / $market1['BidPrice'];
                $order = drupal_json_decode(api_query_cr("SubmitTrade", array(
                    'TradePairId' => $chain['markets'][1]['TradePairId'],
                    'Type' => 'Sell',
                    'Rate' => $market1['BidPrice'],
                    'Amount' => $amount
                )));
                // если неудачно - не продолжать
                if (!$order['Success']) return;
                // если ордер не выполнен сразу - отменить его
                if (!empty($order['Data']['OrderId'])) {
                    do {
                        $cancelOrder = drupal_json_decode(api_query_cr("CancelTrade", array(
                            'Type' => 'Trade',
                            'OrderId' => $order['Data']['OrderId'],
                        )));
                    } while ($cancelOrder['Success']);
                }

                // продать
                $amount = $volumeBTC / $market2['BidPrice'];
                $order = drupal_json_decode(api_query_cr("SubmitTrade", array(
                    'TradePairId' => $chain['markets'][2]['TradePairId'],
                    'Type' => 'Sell',
                    'Rate' => $market2['BidPrice'],
                    'Amount' => $amount
                )));
                // если неудачно - не продолжать
                if (!$order['Success']) return;
                // если ордер не выполнен сразу - отменить его
                if (!empty($order['Data']['OrderId'])) {
                    do {
                        $cancelOrder = drupal_json_decode(api_query_cr("CancelTrade", array(
                            'Type' => 'Trade',
                            'OrderId' => $order['Data']['OrderId'],
                        )));
                    } while ($cancelOrder['Success']);
                }
            } // обратная цепочка
            else {
                // купить
                $amount = $volumeBTC / $market2['AskPrice'];
                $order = drupal_json_decode(api_query_cr("SubmitTrade", array(
                    'TradePairId' => $chain['markets'][3]['TradePairId'],
                    'Type' => 'Buy',
                    'Rate' => $market0['AskPrice'],
                    'Amount' => $amount
                )));
                // если неудачно - не продолжать
                if (!$order['Success']) return;
                // если ордер не выполнен сразу - отменить его
                if (!empty($order['Data']['OrderId'])) {
                    do {
                        $cancelOrder = drupal_json_decode(api_query_cr("CancelTrade", array(
                            'Type' => 'Trade',
                            'OrderId' => $order['Data']['OrderId'],
                        )));
                    } while ($cancelOrder['Success']);
                }

                // купить
                $amount = $volumeBTC / $market2['BidPrice'] / $market1['AskPrice'];
                $order = drupal_json_decode(api_query_cr("SubmitTrade", array(
                    'TradePairId' => $chain['markets'][1]['TradePairId'],
                    'Type' => 'Buy',
                    'Rate' => $market1['AskPrice'],
                    'Amount' => $amount
                )));
                // если неудачно - не продолжать
                if (!$order['Success']) return;
                // если ордер не выполнен сразу - отменить его
                if (!empty($order['Data']['OrderId'])) {
                    do {
                        $cancelOrder = drupal_json_decode(api_query_cr("CancelTrade", array(
                            'Type' => 'Trade',
                            'OrderId' => $order['Data']['OrderId'],
                        )));
                    } while ($cancelOrder['Success']);
                }

                // продать
                $amount = $volumeBTC / $market2['BidPrice'];
                $order = drupal_json_decode(api_query_cr("SubmitTrade", array(
                    'TradePairId' => $chain['markets'][2]['TradePairId'],
                    'Type' => 'Sell',
                    'Rate' => $market2['BidPrice'],
                    'Amount' => $amount
                )));
                // если неудачно - не продолжать
                if (!$order['Success']) return;
                // если ордер не выполнен сразу - отменить его
                if (!empty($order['Data']['OrderId'])) {
                    do {
                        $cancelOrder = drupal_json_decode(api_query_cr("CancelTrade", array(
                            'Type' => 'Trade',
                            'OrderId' => $order['Data']['OrderId'],
                        )));
                    } while ($cancelOrder['Success']);
                }
            }
        }
        next($data['chains']);
    }
}

function _set_profit_volumes_cr(&$data)
{
    if (isset($data['orders'])) {
        $orders = $data['orders'];
        $markets = $data['markets'];
        $chains = $data['chains'];
        $volumeFwdBTC = $volumeBwdBTC = 0;

        // из всех пар цепочки найти минимальный объём
        foreach($chains as $key => $chain) {
            // если есть ордера на все рынки
            if (isset($orders[$chain['markets'][0]]) && isset($orders[$chain['markets'][1]]) && $orders[$chain['markets'][2]]) {

                // если профитна прямая цепочка
                if ($chain['analisys']['fwd'] > 1) {
                    $price0 = $orders[$chain['markets'][0]]['Sell'][0]['Price'];
                    $price1 = $orders[$chain['markets'][1]]['Buy'][0]['Price'];
                    $price2 = $orders[$chain['markets'][2]]['Buy'][0]['Price'];

                    // проверить, не изменилась ли цена со времени запроса Рынков
                    if ($price0 == $markets[$chain['markets'][0]]['AskPrice']
                        && $price1 == $markets[$chain['markets'][1]]['BidPrice']
                        && $price2 == $markets[$chain['markets'][2]]['BidPrice']
                    ) {
                        $volumeBTC0 = $orders[$chain['markets'][0]]['Sell'][0]['Total'];
                        $volumeBTC1 = $orders[$chain['markets'][1]]['Buy'][0]['Total'] * $price2;
                        $volumeBTC2 = $orders[$chain['markets'][2]]['Buy'][0]['Total'];

                        $volumeFwdBTC = $volumeBTC0;
                        $volumeFwdBTC = ($volumeFwdBTC > $volumeBTC1) ? $volumeBTC1 : $volumeFwdBTC;
                        $volumeFwdBTC = ($volumeFwdBTC > $volumeBTC2) ? $volumeBTC2 : $volumeFwdBTC;
                    }
                }

                // если профитна обратная цепочка
                if ($chain['analisys']['bwd'] > 1) {
                    $price0 = $orders[$chain['markets'][0]]['Buy'][0]['Price'];
                    $price1 = $orders[$chain['markets'][1]]['Sell'][0]['Price'];
                    $price2 = $orders[$chain['markets'][2]]['Sell'][0]['Price'];

                    // проверить, не изменилась ли цена со времени запроса Рынков
                    if ($price0 == $markets[$chain['markets'][0]]['BidPrice']
                        && $price1 == $markets[$chain['markets'][1]]['AskPrice']
                        && $price2 == $markets[$chain['markets'][2]]['AskPrice']
                    ) {
                        $volumeBTC0 = $orders[$chain['markets'][0]]['Buy'][0]['Total'];
                        $volumeBTC1 = $orders[$chain['markets'][1]]['Sell'][0]['Total'] * $price2;
                        $volumeBTC2 = $orders[$chain['markets'][2]]['Sell'][0]['Total'];

                        $volumeBwdBTC = $volumeBTC0;
                        $volumeBwdBTC = ($volumeBwdBTC > $volumeBTC1) ? $volumeBTC1 : $volumeBwdBTC;
                        $volumeBwdBTC = ($volumeBwdBTC > $volumeBTC2) ? $volumeBTC2 : $volumeBwdBTC;
                    }
                }

                if ($chain['analisys']['fwd'] > $chain['analisys']['bwd']) $data['chains'][$key]['analisys']['volumeBTC'] = $volumeFwdBTC;
                else  $data['chains'][$key]['analisys']['volumeBTC'] = $volumeBwdBTC;
            }
        }
    }
}

function _get_volumes_cr(&$data)
{
    $markets = array();
    $chains = $data['chains'];

    reset($chains);
    while ($chain = current($chains)) {
        // только для профитных цепочек
        if ($chain['analisys']['fwd'] > 1 || $chain['analisys']['bwd'] > 1) {
            $markets[$chain['markets'][0]] = $chain['markets'][0];
            $markets[$chain['markets'][1]] = $chain['markets'][1];
            $markets[$chain['markets'][2]] = $chain['markets'][2];
        } else break;
        next($chains);
    };
    if ($markets) {
        $orders = drupal_json_decode(api_query_cr("GetMarketOrderGroups", array(implode('-', $markets))));

        if ($orders['Success']) {
            foreach ($orders['Data'] as $order) {
                $data['orders'][$order['TradePairId']] = $order;
            }
        }
    }
}

function _set_chains_cr(&$data)
{
    $chains = array();
    $markets = array();
    $coins = array();

    foreach($data['markets'] as $item) {
        $cns = explode('/', $item['Label']);
        $item['coin1'] = $cns[0];
        $item['coin2'] = $cns[1];
        $item['name'] = $item['Label'];
        $markets[$item['TradePairId']] = $item;

        // массив монет с определением пар для каждой
        $coins[$item['coin1']][$item['coin2']] = $item['TradePairId'];
    }

    foreach($markets as $mid => $market) {

        // берем кросс пару, у монет должны быть пары с BTC
        if ($market['coin2'] != 'BTC' && !empty($coins[$market['coin1']]['BTC']) && !empty($coins[$market['coin2']]['BTC'])) {
            $pair1 = $market['coin1'] . '/BTC';
            $pair2 = $market['Label'];
            $pair3 = $market['coin2'] . '/BTC';

            // если пары активны
            if ($data['pairs'][$pair1]['Status'] == 'OK' && $data['pairs'][$pair2]['Status'] == 'OK' && $data['pairs'][$pair3]['Status'] == 'OK') {
                // ищем для монет пары с BTC
                $chains[]['markets'] = array(
                    0 => $coins[$market['coin1']]['BTC'],
                    1 => $mid,
                    2 => $coins[$market['coin2']]['BTC'],
                );
            }
        }
    }

    $data['chains'] = $chains;
    $data['markets'] = $markets;
}

function _make_analisys_cr(&$data)
{
    $fee = .99803392;
    $chains = array();
    $markets = $data['markets'];

    foreach ($data['chains'] as $cid => $chain) {
        // вычисление прямой сделки
        $fwd2 = 0;
        if (!empty($markets[$chain['markets'][0]]['AskPrice']) && !empty($markets[$chain['markets'][1]]['BidPrice']) && !empty($markets[$chain['markets'][2]]['BidPrice'])) {
            $fwd0 = 1 / $markets[$chain['markets'][0]]['AskPrice'] * $fee;
            $fwd1 = $fwd0 * $markets[$chain['markets'][1]]['BidPrice'] * $fee;
            $fwd2 = $fwd1 * $markets[$chain['markets'][2]]['BidPrice'] * $fee;
        }
        $data['chains'][$cid]['analisys']['fwd'] = $fwd2;

        // вычисление обратной сделки
        $bwd2 = 0;
        if (!empty($markets[$chain['markets'][2]]['AskPrice']) && !empty($markets[$chain['markets'][1]]['AskPrice']) && !empty($markets[$chain['markets'][0]]['BidPrice'])) {
            $bwd0 = 1 / $markets[$chain['markets'][2]]['AskPrice'] * $fee;
            $bwd1 = $bwd0 / $markets[$chain['markets'][1]]['AskPrice'] * $fee;
            $bwd2 = $bwd1 * $markets[$chain['markets'][0]]['BidPrice'] * $fee;
        }
        $data['chains'][$cid]['analisys']['bwd'] = $bwd2;

        $key = ($fwd2 > $bwd2) ? $fwd2 : $bwd2;
        $key = ceil($key * 1000000);
        $chains[$key] = $data['chains'][$cid];
    }

    krsort($chains);
    $data['chains'] = array_slice($chains,0,10);
}

/**
 * Menu callback.
 */
function orders_ajax_callback($op, $oid = 0, $mode = NULL) {
    // Если у посетителя отключён javascript, то показываем ему сообщение
    if ($mode != 'ajax') {
        drupal_set_message('Enable Javascript');
        drupal_goto(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '<front>');
    }

    switch ($op) {
        case 'delete':
            $result = api_query_cr("CancelTrade", array( 'CancelType' => 'Trade', 'OrderId'=> $oid ));
            break;
    }


    // обновляем содержимое блока
    $commands[] = ajax_command_replace('#bot', bot_main_page());

    return array(
        '#type' => 'ajax',
        '#commands' => $commands,
    );
}



function api_query_cr($method, array $req = array()) {
    $API_KEY = 'e461649e3dad48fd8c15cdc948564fce';
    $API_SEC = 'tRAeV/Co3YhWbOrAyakhKIq8gDJZRB9lOEMuGCVqdks=';
    $public_set = array( "GetCurrencies", "GetTradePairs", "GetMarkets", "GetMarket", "GetMarketHistory", "GetMarketOrders", "GetMarketOrderGroups" );
    $private_set = array( "GetBalance", "GetDepositAddress", "GetOpenOrders", "GetTradeHistory", "GetTransactions", "SubmitTrade", "CancelTrade", "SubmitTip" );

    static $ch = null;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; Cryptopia.co.nz API PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
    if ( in_array( $method ,$public_set ) ) {
        $url = "https://www.cryptopia.co.nz/api/" . $method;
        if ($req) { foreach ($req as $r ) { $url = $url . '/' . $r; } }
        curl_setopt($ch, CURLOPT_URL, $url );
    } elseif ( in_array( $method, $private_set ) ) {
        $url = "https://www.cryptopia.co.nz/Api/" . $method;
        $nonce = explode(' ', microtime())[1];
        $post_data = json_encode( $req );
        $m = md5( $post_data, true );
        $requestContentBase64String = base64_encode( $m );
        $signature = $API_KEY . "POST" . strtolower( urlencode( $url ) ) . $nonce . $requestContentBase64String;
        $hmacsignature = base64_encode( hash_hmac("sha256", $signature, base64_decode( $API_SEC ), true ) );
        $header_value = "amx " . $API_KEY . ":" . $hmacsignature . ":" . $nonce;
        $headers = array("Content-Type: application/json; charset=utf-8", "Authorization: $header_value");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $req ) );
    }
    // run the query
    $res = curl_exec($ch);
    if ($res === false) throw new Exception('Could not get reply: '.curl_error($ch));
    return $res;
}
<?php
function bot_page_ex()
{
    $output = '';

    drupal_add_library('system', 'drupal.ajax');

    // получить все рынки
    $result = drupal_http_request('https://api.exmo.com/v1/ticker/');
    if ($result->status_message == 'OK') {
        $data['markets'] = drupal_json_decode($result->data);
    }

    // составить 3x цепочки
    _set_chains_3x_ex($data);

    // составить 4x цепочки
    _set_chains_4x_ex($data);

    // провести анализ
    _make_analisys_ex($data);

    _make_analisys_4x_ex($data);

    $output = theme('bot_table', array('data' => $data));
    return $output;
}



function _make_analisys_4x_ex(&$data)
{
    $fee = .99803392;
    $chains = array();
    $markets = $data['markets'];

    foreach ($data['chains4'] as $cid => $chain) {
        $market0 = $markets[$chain['markets'][0]];
        $m0c1 = $market0['coin1']; $m0c2 = $market0['coin2'];
        $market1 = $markets[$chain['markets'][1]];
        $m1c1 = $market1['coin1']; $m1c2 = $market1['coin2'];
        $market2 = $markets[$chain['markets'][2]];
        $m2c1 = $market2['coin1']; $m2c2 = $market2['coin2'];
        $market3 = $markets[$chain['markets'][3]];
        $m3c1 = $market3['coin1']; $m3c2 = $market3['coin2'];
        $basecoin = $chain['basecoin'];

        // вычисление прямой сделки
        if ($basecoin == $m0c2) {
            $fwd0 = 1 / $market0['sell_price'] * $fee;
        } else {
            $fwd0 = 1 * $market0['buy_price'] * $fee;
        }

        $coin1 = ($basecoin == $m0c2) ? $m0c1 : $m0c2;
        if ($m1c1 == $coin1) {
            $fwd1 = $fwd0 * $market1['buy_price'] * $fee;
        } else {
            $fwd1 = $fwd0 / $market1['sell_price'] * $fee;
        }

        $coin2 = ($coin1 == $m1c2) ? $m1c1 : $m1c2;
        if ($m2c1 == $coin2) {
            $fwd2 = $fwd1 * $market2['buy_price'] * $fee;
        } else {
            $fwd2 = $fwd1 / $market2['sell_price'] * $fee;
        }

        if ($basecoin == $m3c2) {
            $fwd3 = $fwd2 * $market3['buy_price'] * $fee;
        } else {
            $fwd3 = $fwd2 / $market3['sell_price'] * $fee;
        }

        $data['chains4'][$cid]['analisys']['fwd'] = $fwd3;

        // вычисление обратной сделки
        if ($basecoin == $m3c2) {
            $bwd0 = 1 / $market3['sell_price'] * $fee;
        } else {
            $bwd0 = 1 * $market3['buy_price'] * $fee;
        }

        $coin2   = ($basecoin == $m3c2) ? $m3c1 : $m3c2;
        if ($m2c2 == $coin2) {
            $bwd1 = $bwd0 / $market2['sell_price'] * $fee;
        } else {
            $bwd1 = $bwd0 * $market2['buy_price'] * $fee;
        }

        $coin1   = ($coin2 == $m2c2) ? $m2c1 : $m2c2;
        if ($m1c2 == $coin1) {
            $bwd2 = $bwd1 / $market1['sell_price'] * $fee;
        } else {
            $bwd2 = $bwd1 * $market1['buy_price'] * $fee;
        }

        if ($basecoin == $m0c2) {
            $bwd3 = $bwd2 * $market0['buy_price'] * $fee;
        } else {
            $bwd3 = $bwd2 / $market0['sell_price'] * $fee;
        }

        $data['chains4'][$cid]['analisys']['bwd'] = $bwd3;

        $key = ($fwd3 > $bwd3) ? $fwd3 : $bwd3;
        $key = ceil($key * 1000000);
        $chains[$key] = $data['chains4'][$cid];
    }

    krsort($chains);
    $data['chains4'] = array_slice($chains,0,5);
}

function _make_analisys_ex(&$data)
{
    $fee = .99803392;
    $chains = array();
    $markets = $data['markets'];

    foreach ($data['chains'] as $cid => $chain) {
        $market0 = $markets[$chain['markets'][0]];
        $m0c1 = $market0['coin1']; $m0c2 = $market0['coin2'];
        $market1 = $markets[$chain['markets'][1]];
        $m1c1 = $market1['coin1']; $m1c2 = $market1['coin2'];
        $market2 = $markets[$chain['markets'][2]];
        $m2c1 = $market2['coin1']; $m2c2 = $market2['coin2'];
        $basecoin = $chain['basecoin'];

        // вычисление прямой сделки
        if ($basecoin == $m0c2) {
            $fwd0 = 1 / $market0['sell_price'] * $fee;
        } else {
            $fwd0 = 1 * $market0['buy_price'] * $fee;
        }

        $coin = ($basecoin == $m0c2) ? $m0c1 : $m0c2;
        if ($m1c1 == $coin) {
            $fwd1 = $fwd0 * $market1['buy_price'] * $fee;
        } else {
            $fwd1 = $fwd0 / $market1['sell_price'] * $fee;
        }

        if ($basecoin == $m2c2) {
            $fwd2 = $fwd1 * $market2['buy_price'] * $fee;
        } else {
            $fwd2 = $fwd1 / $market2['sell_price'] * $fee;
        }

        $data['chains'][$cid]['analisys']['fwd'] = $fwd2;

        // вычисление обратной сделки
        if ($basecoin == $m2c2) {
            $bwd0 = 1 / $market2['sell_price'] * $fee;
        } else {
            $bwd0 = 1 * $market2['buy_price'] * $fee;
        }

        $coin   = ($basecoin == $m2c2) ? $m2c1 : $m2c2;
        if ($m1c2 == $coin) {
            $bwd1 = $bwd0 / $market1['sell_price'] * $fee;
        } else {
            $bwd1 = $bwd0 * $market1['buy_price'] * $fee;
        }

        if ($basecoin == $m0c2) {
            $bwd2 = $bwd1 * $market0['buy_price'] * $fee;
        } else {
            $bwd2 = $bwd1 / $market0['sell_price'] * $fee;
        }

        $data['chains'][$cid]['analisys']['bwd'] = $bwd2;

        $key = ($fwd2 > $bwd2) ? $fwd2 : $bwd2;
        $key = ceil($key * 1000000);
        $chains[$key] = $data['chains'][$cid];
    }

    krsort($chains);
    $data['chains'] = array_slice($chains,0,5);
}

function _set_chains_3x_ex(&$data)
{
    $chains = array();
    $markets = array();
    $coins = array();

    foreach($data['markets'] as $mid => $item) {
        $cns = explode('_', $mid);
        $item['coin1'] = $cns[0];
        $item['coin2'] = $cns[1];
        $item['AskPrice'] = $item['sell_price'];
        $item['BidPrice'] = $item['buy_price'];
        $item['name'] = $mid;
        $markets[$mid] = $item;

        // массив монет с определением пар для каждой
        $coins[$item['coin1']][$item['coin2']] = $mid;
    }

    // определить все возможные тройки
    foreach($markets as $midL1 => $marketL1) {
        $L1C1 = $marketL1['coin1']; $L1C2 = $marketL1['coin2'];
        foreach($markets as $midL2 => $marketL2) {
            $L2C1 = $marketL2['coin1']; $L2C2 = $marketL2['coin2'];
            foreach($markets as $midL3 => $marketL3) {
                $L3C1 = $marketL3['coin1']; $L3C2 = $marketL3['coin2'];

                // пары не должны повторяться
                if ($midL1 != $midL2 && $midL2 != $midL3 && $midL1 != $midL3) {
                    // вторая пара должна содержать валюту из первой
                    if (($L2C1 == $L1C1 || $L2C1 == $L1C2) || ($L2C2 ==$L1C1 || $L2C2 == $L1C2)) {
                        // третья пара должна содержать валюту из первой и из второй
                        if ((($L3C1 == $L1C1 || $L3C1 == $L1C2) && ($L3C2 == $L2C1 || $L3C2 == $L2C2))
                        || (($L3C1 == $L2C1 || $L3C1 == $L2C2) && ($L3C2 == $L1C1 || $L3C2 == $L1C2))){
                            // определение базовой монеты
                            $basecoin = ($L1C1 == $L3C1 || $L1C1 == $L3C2) ? $L1C1 : $L1C2;
                            $chains[] = array(
                                'markets' => array(
                                    '0' => $midL1,
                                    '1' => $midL2,
                                    '2' => $midL3,
                                ),
                                'basecoin' => $basecoin,
                            );
                        }
                    }
                }
            }
        }
    }

    $data['chains'] = $chains;
    $data['markets'] = $markets;
}
function _set_chains_4x_ex(&$data)
{
    $chains = array();
    $markets = $data['markets'];

    // определить все возможные тройки
    foreach($markets as $midL1 => $marketL1) {
        $L1C1 = $marketL1['coin1']; $L1C2 = $marketL1['coin2'];
        foreach($markets as $midL2 => $marketL2) {
            $L2C1 = $marketL2['coin1']; $L2C2 = $marketL2['coin2'];
            foreach($markets as $midL3 => $marketL3) {
                $L3C1 = $marketL3['coin1']; $L3C2 = $marketL3['coin2'];
                foreach($markets as $midL4 => $marketL4) {
                    $L4C1 = $marketL4['coin1']; $L4C2 = $marketL4['coin2'];

                    // пары не должны повторяться
                    if ($midL1 != $midL2 && $midL1 != $midL3 && $midL1 != $midL4 && $midL2 != $midL3 && $midL2 != $midL4 && $midL3 != $midL4) {
                        // вторая пара должна содержать валюту из первой и третьей
                        if ((($L2C1 == $L1C1 || $L2C1 == $L1C2) && ($L2C2 == $L3C1 || $L2C2 == $L3C2))
                            || ($L2C2 == $L1C1 || $L2C2 == $L1C2) && ($L2C1 == $L3C1 || $L2C1 == $L3C2)) {
                            // третья пара должна содержать валюту из второй
                            if (($L3C1 == $L2C1 || $L3C1 == $L2C2) || ($L3C2 ==$L2C1 || $L3C2 == $L2C2)) {
                                // четвертая пара должна содержать валюту из первой и из третьей и не содержать из второй
                                if (((($L4C1 == $L1C1 || $L4C1 == $L1C2) && ($L4C2 == $L3C1 || $L4C2 == $L3C2))
                                    || (($L4C1 == $L3C1 || $L4C1 == $L3C2) && ($L4C2 == $L1C1 || $L4C2 == $L1C2)))
                                && $L4C1 != $L2C1 && $L4C1 != $L2C2 && $L4C2 != $L2C1 && $L4C2 != $L2C2){

                                    // определение базовой монеты
                                    $basecoin = ($L1C1 == $L4C1 || $L1C1 == $L4C2) ? $L1C1 : $L1C2;
                                    $chains[] = array(
                                        'markets' => array(
                                            '0' => $midL1,
                                            '1' => $midL2,
                                            '2' => $midL3,
                                            '3' => $midL4,
                                        ),
                                        'basecoin' => $basecoin,
                                    );

                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $data['chains4'] = $chains;
}


function api_query_ex($api_name, array $req = array())
{
    $mt = explode(' ', microtime());
    $NONCE = $mt[1] . substr($mt[0], 2, 6);

    // API settings
    $key = ""; //TODO replace with your api key from profile page
    $secret = ""; //TODO replace with your api secret from profile page

    $url = "http://api.exmo.com/v1/$api_name";

    $req['nonce'] = $NONCE;

    // generate the POST data string
    $post_data = http_build_query($req, '', '&');

    $sign = hash_hmac('sha512', $post_data, $secret);

    // generate the extra headers
    $headers = array(
        'Sign: ' . $sign,
        'Key: ' . $key,
    );

    // our curl handle (initialize if required)
    static $ch = null;
    if (is_null($ch)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; PHP client; ' . php_uname('s') . '; PHP/' . phpversion() . ')');
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    // run the query
    $res = curl_exec($ch);
    if ($res === false) throw new Exception('Could not get reply: ' . curl_error($ch));

    $dec = json_decode($res, true);
    if ($dec === null)
        throw new Exception('Invalid data received, please make sure connection is working and requested API exists');

    return $dec;
}
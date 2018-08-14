<?php
function arbitr_page_btcz()
{
    $output = '';

    drupal_add_css(drupal_get_path('module', 'arbitr') . '/css/arbitr.css');

    // получить все рынки
    $result = drupal_http_request('http://www.bit-z.com/api_v1/tickerall');
    if ($result->status_message == 'OK') {
        $markets = drupal_json_decode($result->data)['data'];
    }

    // составить 3x цепочки
    $chains = btcz_find_chains_3x($markets);


    $output = theme('arbitr_table', array('chains' => $chains, 'markets' => $markets));

    return $output;
}


function btcz_find_chains_3x($data)
{
    $chains = array();
    $markets = array();
    $coins = array();

    // подготовить данные для формирования цепей
    foreach($data as $mid => $item) {
        $cns = explode('_', $mid);
        $item['coin1'] = $cns[0];
        $item['coin2'] = $cns[1];
        // за сколько я могу купить
        $item['ask'] = $item['sell'];
        $item['ask'] = $mid == 'btc_usdt' ? 10000 : $item['sell'];
        $item['ask'] = $mid == 'eth_usdt' ? 900 : $item['sell'];
//        $item['ask'] = $item['sell'];
        // за сколько я могу продать
        $item['bid'] = $item['buy'];
        $item['bid'] = $mid == 'btc_usdt' ? 9950 : $item['buy'];
        $item['bid'] = $mid == 'eth_usdt' ? 890 : $item['buy'];
//        $item['bid'] = $item['buy'];
        $item['name'] = $mid;
        $markets[$mid] = $item;

        // массив монет с определением пар для каждой
        $coins[$item['coin1']][$item['coin2']] = $mid;
    }

    ksort($coins);
    // определить все возможные тройки
    foreach($coins as $sym_X => $coin) {
        if (count($coin) > 1) {
            $sym_A = array_keys($coin)[0];
            $sym_B = array_keys($coin)[1];

            // найти для пары монет A и B массив общих монет Y
            if (isset($coins[$sym_A]) && isset($coins[$sym_B])
                && $coins_Y = array_keys(array_intersect_key($coins[$sym_A], $coins[$sym_B])))
            {
                $sym_Y = $coins_Y[0];

                $sym_X_A = $sym_X . '_' . $sym_A;
                $sym_X_B = $sym_X . '_' . $sym_B;
                $sym_A_Y = $sym_A . '_' . $sym_Y;
                $sym_B_Y = $sym_B . '_' . $sym_Y;

                $price_X_A_buy  = $markets[$sym_X_A]['ask'];
                $price_X_A_sell = $markets[$sym_X_A]['bid'];
                $price_X_B_buy  = $markets[$sym_X_B]['ask'];
                $price_X_B_sell = $markets[$sym_X_B]['bid'];
                $price_A_Y_buy  = $markets[$sym_A_Y]['ask'];
                $price_A_Y_sell = $markets[$sym_A_Y]['bid'];
                $price_B_Y_buy  = $markets[$sym_B_Y]['ask'];
                $price_B_Y_sell = $markets[$sym_B_Y]['bid'];

                // $koef должен быть меньше 1
                // todo определить насколько меньше 1, чтобы был профит (комиссии, волатильность и прочее)
                $koef_fwd = ($price_X_B_buy * $price_B_Y_buy) / ($price_X_A_sell * $price_A_Y_sell);
                $koef_bwd = ($price_X_A_buy * $price_A_Y_buy) / ($price_X_B_sell * $price_B_Y_sell);

                $chains[] = array(
                    'x_a' => $sym_X_A,
                    'x_a_sell' => $price_X_A_sell,
                    'x_a_usd' => number_format($price_X_A_sell * $price_A_Y_sell, 2, ',', ''),
                    'x_b' => $sym_X_B,
                    'x_b_buy' => $price_X_B_buy,
                    'x_b_usd' => number_format($price_X_B_sell * $price_B_Y_sell, 2, ',', ''),
                    'a_y' => $sym_A_Y,
                    'a_y_sell' => $price_A_Y_sell,
                    'b_y' => $sym_B_Y,
                    'b_y_buy' => $price_B_Y_buy,
                    'koef_fwd' => number_format($koef_fwd, 4, ',', ''),
                    'koef_bwd' => number_format($koef_bwd, 4, ',', ''),
                );
            }
        }
    }


    return $chains;
}


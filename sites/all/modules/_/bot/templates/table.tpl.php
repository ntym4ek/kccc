<?
$chains = $data['chains'];
$chains4 = $data['chains4'];
$markets = $data['markets'];
$my_orders = empty($data['my_orders']) ? false : $data['my_orders'];
?>

<div id="bot" class="bot">
    <h3>Аналитика</h3>
    <table class="bot-table">
        <tr>
            <th colspan="2">Пара 1</th>
            <th colspan="2">Пара 2</th>
            <th colspan="2">Пара 3</th>
            <th>Объём, BTC</th>
            <th width="10%">Коэффициент FWD</th>
            <th width="10%">Коэффициент BWD</th>
        </tr>
        <? foreach($chains as $chain): ?>
        <tr>
            <td rowspan="2"><? print $markets[$chain['markets'][0]]['name']; ?></td>
            <td><? print number_format($markets[$chain['markets'][0]]['BidPrice'], 8, ',', ''); ?></td>
            <td rowspan="2"><? print $markets[$chain['markets'][1]]['name']; ?></td>
            <td><? print number_format($markets[$chain['markets'][1]]['BidPrice'], 8, ',', ''); ?></td>
            <td rowspan="2"><? print $markets[$chain['markets'][2]]['name']; ?></td>
            <td><? print number_format($markets[$chain['markets'][2]]['BidPrice'], 8, ',', ''); ?></td>
            <td rowspan="2"><? print number_format(isset($chain['analisys']['volumeBTC']) ? $chain['analisys']['volumeBTC'] : 0, 10, ',', ''); ?></td>
            <td rowspan="2"<? print ($chain['analisys']['fwd'] > 1) ? ' class="b-green c-white"' : ''; ?>><? print number_format($chain['analisys']['fwd'], 4, ',', ''); ?></td>
            <td rowspan="2"<? print ($chain['analisys']['bwd'] > 1) ? ' class="b-green c-white"' : ''; ?>><? print number_format($chain['analisys']['bwd'], 4, ',', ''); ?></td>
        </tr>
        <tr>
            <td><? print number_format($markets[$chain['markets'][0]]['AskPrice'], 8, ',', ''); ?></td>
            <td><? print number_format($markets[$chain['markets'][1]]['AskPrice'], 8, ',', ''); ?></td>
            <td><? print number_format($markets[$chain['markets'][2]]['AskPrice'], 8, ',', ''); ?></td>
        </tr>
        <? endforeach; ?>
    </table>

    <? if (isset($chains4)): ?>
    <h3>Аналитика 4x</h3>
    <table class="bot-table">
        <tr>
            <th colspan="2">Пара 1</th>
            <th colspan="2">Пара 2</th>
            <th colspan="2">Пара 3</th>
            <th colspan="2">Пара 4</th>
            <th width="10%">Коэффициент FWD</th>
            <th width="10%">Коэффициент BWD</th>
        </tr>
        <? foreach($chains4 as $chain): ?>
        <tr>
            <td rowspan="2"><? print $markets[$chain['markets'][0]]['name']; ?></td>
            <td><? print number_format($markets[$chain['markets'][0]]['BidPrice'], 6, ',', ''); ?></td>
            <td rowspan="2"><? print $markets[$chain['markets'][1]]['name']; ?></td>
            <td><? print number_format($markets[$chain['markets'][1]]['BidPrice'], 6, ',', ''); ?></td>
            <td rowspan="2"><? print $markets[$chain['markets'][2]]['name']; ?></td>
            <td><? print number_format($markets[$chain['markets'][2]]['BidPrice'], 6, ',', ''); ?></td>
            <td rowspan="2"><? print $markets[$chain['markets'][3]]['name']; ?></td>
            <td><? print number_format($markets[$chain['markets'][3]]['BidPrice'], 6, ',', ''); ?></td>
            <td rowspan="2"<? print ($chain['analisys']['fwd'] > 1) ? ' class="b-green c-white"' : ''; ?>><? print number_format($chain['analisys']['fwd'], 4, ',', ''); ?></td>
            <td rowspan="2"<? print ($chain['analisys']['bwd'] > 1) ? ' class="b-green c-white"' : ''; ?>><? print number_format($chain['analisys']['bwd'], 4, ',', ''); ?></td>
        </tr>
        <tr>
            <td><? print number_format($markets[$chain['markets'][0]]['AskPrice'], 6, ',', ''); ?></td>
            <td><? print number_format($markets[$chain['markets'][1]]['AskPrice'], 6, ',', ''); ?></td>
            <td><? print number_format($markets[$chain['markets'][2]]['AskPrice'], 6, ',', ''); ?></td>
            <td><? print number_format($markets[$chain['markets'][3]]['AskPrice'], 6, ',', ''); ?></td>
        </tr>
        <? endforeach; ?>
    </table>
    <? endif; ?>

    <? if (!empty($my_orders)): ?>
        <h3>Открытые ордера</h3>
        <table class="bot-table">
            <tr>
                <th width="15%">Пара</th>
                <th width="10%">Тип</th>
                <th>Цена</th>
                <th>Кол-во</th>
                <th>Итого</th>
                <th>Действия</th>
            </tr>
            <? foreach($my_orders as $order): ?>
                <tr>
                    <td><? print $order['Market']; ?></td>
                    <td><? print $order['Type']; ?></td>
                    <td><? print number_format($order['Rate'], 8, ',', ''); ?></td>
                    <td><? print $order['Amount']; ?></td>
                    <td><? print number_format($order['Total'], 6, ',', ''); ?></td>
                    <?
                    $link = l('x', 'bot/orders/delete/' . $order['OrderId'] . '/nojs', array(
                        'attributes' => array(
                            'class' => array('use-ajax'),
                            'title' => 'Delete',
                        )
                    ));
                    ?>
                    <td><? print $link; ?></td>
                </tr>
            <? endforeach; ?>
        </table>
    <? endif; ?>
</div>
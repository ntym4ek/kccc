<?
$msg = isset($data['messages']) ? $data['messages'] : '';
$trades = $data['open_trades'];
?>
<div id="open-trades">
<? if(isset($trades)): ?>
    <h3>Открытые сделки</h3>
    <table class="bot-table">
        <tr>
            <th>Пара</th>
            <th>Тип</th>
            <th>Цена BUY</th>
            <th>Цена SELL</th>
            <th width="15%">Количество</th>
            <th width="10%">Время</th>
        </tr>
        <?
        $counter = 0;
        do {
            $open = $class = '';
            $trade = current($trades);
            if (isset($msg[$trade['tid']])) {
                $open   = ($msg[$trade['tid']] == MSG_OPEN) ? ' class="b-orange"' : '';
                $class  = ($msg[$trade['tid']] == MSG_PROFIT_UP) ? ' class = "b-green c-white"' : $class;
                $class  = ($msg[$trade['tid']] == MSG_PROFIT_DOWN) ? ' class = "b-red c-white"' : $class;
            }
        ?>
        <tr<? print $open; ?>>
            <td><? print isset($trade['name']) ? $trade['name'] : ''; ?></td>
            <td><? print isset($trade['type']) ? $trade['type'] : ''; ?></td>
            <td<? print $class && $trade['type'] == 'BUY' ? $class : ''; ?>><? print isset($trade['buy']) ? $trade['buy'] : ''; ?></td>
            <td<? print $class && $trade['type'] == 'SELL' ? $class : ''; ?>><? print isset($trade['sell']) ? $trade['sell'] : ''; ?></td>
            <td><? print isset($trade['amount']) ? number_format($trade['amount'], 6, ',', '') : ''; ?></td>
            <td><? print $trade['created'] ? format_date($trade['created'], 'custom', 'H:i:s') : ''; ?></td>
        </tr>
    <?      $counter++ ;
        } while(next($trades) || $counter < MAX_OPEN_TRADES); ?>
    </table>
<? endif; ?>
</div>

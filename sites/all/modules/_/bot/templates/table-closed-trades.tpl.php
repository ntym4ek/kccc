<?

?>
<div id="closed-trades">
<? if(isset($trades)): ?>
    <h3>Закрытые сделки</h3>
    <table class="bot-table">
        <tr>
            <th>Пара</th>
            <th>Цена BUY</th>
            <th>Цена SELL</th>
            <th width="15%">Профит</th>
            <th width="15%">Время</th>
        </tr>
        <? foreach($trades as $trade): ?>
        <tr>
            <td><? print $trade['name']; ?></td>
            <td><? print $trade['buy']; ?></td>
            <td><? print $trade['sell']; ?></td>
            <td><? print number_format($trade['profit'], 3, ',', ''); ?></td>
            <td><? print $trade['created'] ? format_date($trade['created'], 'custom', 'H:i:s') : ''; ?></td>
        </tr>
    <? endforeach; ?>
    </table>
<? endif; ?>
</div>

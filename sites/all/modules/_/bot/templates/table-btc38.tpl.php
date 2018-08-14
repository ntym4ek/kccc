<?
$pairs = isset($data['pairs']) ? $data['pairs'] : '';

?>
<div id="btc38">
<? if(!empty($pairs)): ?>
    <h3>Аналитика</h3>
    <table class="bot-table">
        <tr>
            <th>Пара</th>
            <th>Buy</th>
            <th>Sell</th>
            <th>Объём, CNY</th>
            <th width="10%">Профит, %</th>
        </tr>
        <? foreach($pairs as $pair): ?>
        <tr>
            <td><? print $pair['name']; ?></td>
            <td><? print number_format($pair['buy'], 8, ',', ''); ?></td>
            <td><? print number_format($pair['sell'], 8, ',', ''); ?></td>
            <td align="right"><? print number_format($pair['volCNY'], 0, ',', ' '); ?></td>
            <td><? print number_format($pair['profit']*100-100, 6, ',', ''); ?></td>
        </tr>
    <? endforeach; ?>
    </table>
<? endif; ?>
</div>

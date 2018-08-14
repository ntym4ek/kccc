<?php

?>

<div class="arbitr">
    <h3>Аналитика</h3>
    <table class="arbitr-table table table-bordered">
        <tr>
            <th></th>
            <th colspan="3">Пары X</th>
            <th colspan="2">Пары Y</th>
            <th width="10%">Коэффициенты</th>
        </tr>
        <? foreach($chains as $chain): ?>
        <tr>
            <td rowspan="2"></td>
            <td><? print $chain['x_a']; ?></td>
            <td><? print $chain['x_a_sell']; ?></td>
            <td align="right"><? print '$' . $chain['x_a_usd']; ?></td>
            <td><? print $chain['a_y']; ?></td>
            <td><? print $chain['a_y_sell']; ?></td>

            <td align="right" <? print ($chain['koef_fwd'] < 1) ? ' class="success"' : ''; ?>><? print $chain['koef_fwd'] . ' fwd'; ?></td>
        </tr>
        <tr>
            <td><? print $chain['x_b']; ?></td>
            <td><? print $chain['x_b_buy']; ?></td>
            <td align="right"><? print '$' . $chain['x_b_usd']; ?></td>
            <td><? print $chain['b_y']; ?></td>
            <td><? print $chain['b_y_buy']; ?></td>
            <td align="right" <? print ($chain['koef_bwd'] < 1) ? ' class="success"' : ''; ?>><? print $chain['koef_bwd'] . ' bwd'; ?></td>
        </tr>
        <? endforeach; ?>
    </table>
</div>
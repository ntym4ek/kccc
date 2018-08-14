<?
    $open_trades = isset($content['open_trades']) ? $content['open_trades'] : '';
    $messages = isset($content['messages']) ? $content['messages'] : '';
    $closed_trades = isset($content['closed_trades']) ? $content['closed_trades'] : '';
    $btc38 = isset($content['btc38']) ? $content['btc38'] : '';
?>

<div id="bot" class="bot">
    <p>
        <button type="button" name="start" id="start-button">Старт</button>
        <button type="button" name="stop" id="stop-button">Стоп</button>
    </p>

    <div>
        <? print $open_trades; ?>
        <? print $closed_trades; ?>
        <? print $messages; ?>
    </div>


    <? print $btc38; ?>
</div>
<?php
/**
 * Created by PhpStorm.
 * User: ntym
 * Date: 20.07.2015
 * Time: 14:27
 */
$order = $variables['order'];
?>

<div class="row checkout-complete">
    <div class="col-sm-12">
        <h4><?php print t('An e-mail with the information below has been sent to your e-mail address.')?></h4>

        <dl class="dl-horizontal">
            <dt><?php print t('Request number')?></dt>
            <dd>№ <?php print $order['number']; ?><br /><p class="text-muted"><?php print t('For tracking in your account')?></p></dd>

            <dt><?php print t('User data')?></dt>
            <dd>
                <?php print $order['user_region']; ?><br />
                <?php print $order['user_name']; ?><br />
                <?php print $order['user_phone']; ?><br />
                <?php print $order['user_email']; ?><br />

            </dd>

            <dt><?php print t('Preparations')?></dt>
            <dd>
                <?php print $order['items_count']; ?><br />
                <?php if (is_array($order['weight'])): ?><?php print t('Weight') . ': ' . $order['weight']['weight'] . ' ' . t($order['weight']['unit']); endif; ?>
                <?php if (is_array($order['volume'])): ?><?php print t('Volume') . ': ' . number_format($order['volume']['volume'], 2) . ' ' . t('m<span class="sup">3</span>'); endif; ?>
            </dd>

            <dt><?php print t('Representative')?></dt>
            <dd>
                <?php print $order['contact_card']; ?>
            </dd>
        </dl>
    </div>
</div>
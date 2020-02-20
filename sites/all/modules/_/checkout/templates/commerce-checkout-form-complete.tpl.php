<?php
/**
 * Created by PhpStorm.
 * User: ntym
 * Date: 20.07.2015
 * Time: 14:27
 */
$data = $variables['data'];
?>

<div class="row checkout-complete">
    <div class="col-sm-12">
        <h4><?php print t('An e-mail with the information below has been sent to your e-mail address.')?></h4>

        <dl class="dl-horizontal">
            <dt><?php print t('Request number')?></dt>
            <dd>№ <?php print $data['number']; ?><br /><p class="text-muted"><?php print t('For tracking in your account')?></p></dd>

            <dt><?php print t('User data')?></dt>
            <dd>
                <?php print $data['user_region']; ?><br />
                <?php print $data['user_name']; ?><br />
                <?php print $data['user_phone']; ?><br />
                <?php print $data['user_email']; ?><br />

            </dd>

            <dt><?php print t('Preparations')?></dt>
            <dd>
                <?php print $data['items_count']; ?><br />
                <?php if (is_array($data['weight'])): ?><?php print t('Weight') . ': ' . $data['weight']['weight'] . ' ' . t($data['weight']['unit']); endif; ?>
                <?php if (is_array($data['volume'])): ?><?php print t('Volume') . ': ' . number_format($data['volume']['volume'], 2) . ' ' . t('m<span class="sup">3</span>'); endif; ?>
            </dd>

            <dt><?php print t('Representative')?></dt>
            <dd>
                <?php print $data['contact_card']; ?>
            </dd>
        </dl>
    </div>
</div>
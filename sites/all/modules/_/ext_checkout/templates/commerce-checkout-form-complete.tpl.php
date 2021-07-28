<div class="row checkout-complete">
  <div class="col-sm-12">
    <h4><?php print t('An e-mail with the information below has been sent to your e-mail address.')?></h4>

    <dl class="dl-horizontal">
      <dt><?php print t('Request number')?></dt>
      <dd>
        № <?php print $order_info["number"]; ?><br />
        <p class="text-muted"><?php print t('For tracking in your account')?></p>
      </dd>

      <dt><?php print t('User data')?></dt>
      <dd>
        <?php print $order_info["user"]["name"]; ?><br />
        <?php if ($order_info["user"]["mail"]) print $order_info["user"]["mail"] . '<br />'; ?>
        <?php if ($order_info["user"]["phone"]) print $order_info["user"]["phone"] . '<br />'; ?>
      </dd>

      <? if ($order_info["services"]): ?>
      <dt><?php print t('Services')?></dt>
      <dd>
        <?php print $order_info["services"]['list']; ?><br />
      </dd>
      <? endif; ?>

      <? if (!empty($order_info["shipping"])): ?>
      <dt><?php print t('Delivery')?></dt>
      <dd>
        <?php print $order_info["shipping"]["title"]; ?><br />
        <?php print $order_info["shipping"]["address"]; ?><br />
      </dd>
      <? endif; ?>

      <? if (!empty($order_info["services"]["support"])): ?>
      <dt><?php print t('Support')?></dt>
      <dd>
        <?php print $order_info["services"]["support"]; ?><br />
      </dd>
      <? endif; ?>

      <dt><?php print t('Payment')?></dt>
      <dd>
        <?php print $order_info["payment"]["title"]; ?><br />
        <p class="text-muted"><?php print $order_info["payment"]["description"]; ?></p>
      </dd>

      <dt><?php print t('Preparations')?></dt>
      <dd>
        <?php print $order_info["products"]["qty_pcs_formatted"]; ?><br />
        <?php if (is_array($order_info["weight"])): ?><?php print t('Weight') . ': ' . $order_info["weight"]["weight"] . ' ' . t($order_info["weight"]["unit"]); endif; ?>
        <?php if (is_array($order_info['volume'])): ?><?php print t('Volume') . ': ' . number_format($order_info['volume']['volume'], 2) . ' ' . t('m<span class="sup">3</span>'); endif; ?>
      </dd>

    </dl>
  </div>
</div>

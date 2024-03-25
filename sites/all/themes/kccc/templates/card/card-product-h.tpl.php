<?php
?>

<?php if (!$inline_link): ?>
<a href="<?php print $product['path']; ?>">
<?php endif; ?>
  <div class="card card-product-h">
    <div class="product">
      <div class="image">
        <?php if ($inline_link): ?><a href="<?php print $product['path']; ?>"><?php endif; ?>
          <img src="<?php print $product['image']['teaser']; ?>" alt="<?php print $product['title']; ?>">
        <?php if ($inline_link): ?></a><?php endif; ?>
      </div>
      <div class="info">
        <div class="title"><?php if ($inline_link): ?><a href="<?php print $product['path']; ?>"><?php endif; ?><?php print $product['label']; ?><?php if ($inline_link): ?></a><?php endif; ?></div>
        <div class="components"><?php print $product['components']['formatted']; ?></div>
        <div class="summary"><?php print $product['summary']; ?></div>
      </div>
    </div>
    <?php if (!empty($addon)): ?>
      <div class="addon">
        <?php foreach ($addon as $item): ?>
        <div class="item">
          <div class="hobjects">
            <?php print (empty($item["hobjects"]["formatted"]) ? $item["info"]["hobjects"]["formatted"] : $item["hobjects"]["formatted"]); ?>
          </div>
          <?php if (!empty($item["info"]["period"]["formatted"])): ?>
          <div class="period"><?php print $item["info"]["period"]["formatted"]; ?></div>
          <?php endif; ?>
          <div class="specs">
            <div class="spec">
              <div class="label"><?php print t('use rate'); ?></div>
              <div class="text"><?php print $item["info"]['prep_spends']['formatted']; ?></div>
            </div>
            <div class="spec">
              <div class="label"><?php print t('use rate per field'); ?></div>
              <div class="text"><?php print $item["info"]['prep_spends']['field']['formatted']; ?></div>
            </div>
            <div class="spec description">
              <a data-id="tooltip-<?php print $item["info"]['id']; ?>"><?php print t('usage'); ?></a>
              <div id="tooltip-<?php print $item["info"]['id']; ?>" class="tooltip"><?php print $item["info"]['description']; ?></div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php if (!$inline_link): ?>
</a>
<?php endif; ?>

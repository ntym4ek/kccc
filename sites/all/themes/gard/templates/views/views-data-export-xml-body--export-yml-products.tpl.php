<?php foreach ($themed_rows as $count => $row): ?>
  <offer id="<?php echo $row['nid']; ?>" available="true">
    <name><?php echo str_replace('*', '', $row['title']); ?></name>
    <description><?php echo htmlspecialchars(trim(strip_tags($row['body']))); ?></description>
    <url><?php echo url('node/' . $row['nid'], ['absolute' => true]); ?></url>
    <price><?php echo $row['commerce_price']*0.9/100; ?></price>
    <currencyId>RUB</currencyId>
    <categoryId><?php echo $row['field_pd_category']; ?></categoryId>
    <?php if (!empty($row['field_p_images'])): ?>
    <picture><?php echo $row['field_p_images']; ?></picture>
    <?php endif; ?>
    <country_of_origin>Россия</country_of_origin>
    <?php if (!empty($row['field_p_tare'])) :?>
    <param name="Тара"><?php echo $row['field_p_tare']; ?></param>
    <?php endif; ?>
    <?php if (!empty($row['field_p_packaging'])) :?>
    <param name="Объём"><?php echo $row['field_p_packaging']; ?></param>
    <?php endif; ?>
    <?php
      $node_wr = entity_metadata_wrapper('node', $row['nid']);
      if (!empty($node_wr->value()->field_pd_active_ingredients)) {
        foreach ($node_wr->field_pd_active_ingredients->getIterator() as $ingredient_wr) {
          echo '<param name="Действующее вещество">' . $ingredient_wr->field_pd_ai_active_ingredient->name->value() . '</param>';
        }
      }
    ?>
  </offer>

<?php endforeach; ?>

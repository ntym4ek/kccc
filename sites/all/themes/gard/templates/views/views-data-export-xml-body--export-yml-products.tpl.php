<?php foreach ($themed_rows as $count => $row): ?>
  <offer id="<?php echo $row['nid']; ?>" available="true">
    <name><?php echo $row['title']; ?></name>
    <description><?php echo htmlspecialchars(trim($row['body'])); ?></description>
    <url><?php echo url('node/' . $row['nid'], ['absolute' => true]); ?></url>
    <price><?php echo $row['commerce_price']*0.9/100; ?></price>
    <currencyId>RUR</currencyId>
    <categoryId><?php echo $row['field_pd_category']; ?></categoryId>
    <?php if (!empty($row['field_main_img'])): ?>
    <picture><?php echo $row['field_main_img']; ?></picture>
    <?php endif; ?>
    <country_of_origin>Россия</country_of_origin>
    <?php if (!empty($row['field_p_tare'])) :?>
    <param name="Тара"><?php echo $row['field_p_tare']; ?></param>
    <?php endif; ?>
    <?php if (!empty($row['field_p_in_package'])) :?>
    <param name="Объём"><?php echo $row['field_p_in_package']; ?></param>
    <?php endif; ?>
    <?php
      $node_wr = entity_metadata_wrapper('node', $row['nid']);
      foreach ($node_wr->field_pd_active_ingredients->getIterator() as $ingredient_wr) {
        echo '<param name="Действующее вещество">' . $ingredient_wr->field_pd_ai_active_ingredient->name->value() . '</param>';
      }
    ?>
  </offer>

<?php endforeach; ?>

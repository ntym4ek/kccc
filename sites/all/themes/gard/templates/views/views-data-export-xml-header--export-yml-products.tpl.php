<?php print '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="<?php echo date('Y-m-d h:i'); ?>">
  <shop>
    <name><?php echo variable_get('site_name', 'Drupal'); ?></name>
    <company><?php echo variable_get('site_name', 'Drupal'); ?></company>
    <url><?php echo $GLOBALS['base_url']; ?></url>
    <currencies><currency id="RUB" rate="1"/></currencies>

    <?php print views_embed_view('export_yml_categories', 'views_data_export_1'); ?>

    <offers>

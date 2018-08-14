<?php
$location_values = field_get_items('node', $node, 'field_vacancy_location');
$location = $location_values[0]['value'];
$region_values = field_get_items('node', $node, 'field_vacancy_region');
$region = $region_values[0]['value'];
$place = ($location) ? $location : $region;
?>

<article
  class="vacancy teaser contextual-links-region"<?php print $attributes; ?>>
    <!-- если тизер выводится в результатах поиска - добавить заголовок с названием типа-->
    <?php if ($_GET['q'] == 'search'): ?>
        <div class="type"><?php print t('Vacancy'); ?></div>
    <?php endif; ?>

    <div class="content" property="content:encoded">
        <span
          class="date"><?php print format_date($node->created, 'custom', 'd.m.Y'); ?></span>
        <span class="location"><?php print $place; ?></span>
    </div>
    <h3 property="dc:title" class="title">
        <a href="<?php print $node_url; ?>"><?php print $title; ?></a>
    </h3>
</article>

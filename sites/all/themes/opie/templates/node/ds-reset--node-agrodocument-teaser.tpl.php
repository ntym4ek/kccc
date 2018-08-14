<?php
$name_field = field_get_items('node', $node, 'field_doc_name');
$name = $name_field['0']['value'];
?>

<article class="agrodocument teaser contextual-links-region"<?php print $attributes; ?>>
    <!-- если тизер выводится в результатах поиска - добавить заголовок с названием типа-->
    <?php if ($_GET['q'] == 'search'): ?>
        <div class="type"><?php print t('Document'); ?></div>
    <?php endif; ?>

    <div class="content" property="content:encoded">
        <header>
            <h2 property="dc:title" class="title">
                <a href="<?php print $node_url; ?>"><?php print $name; ?></a>
            </h2>
            <?php print $ds_content; ?>
            <?php if (!isset($node->field_doc_link) || !$node->field_doc_link ): ?>
            <div class="field field-read-more">
                <a href="<?php print $node_url; ?>" target="_blank" rel="nofollow"><?php print t('Read more'); ?></a>
            </div>
            <?php endif; ?>
        </header>
    </div>
</article>

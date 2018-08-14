<?php
// Promo-image.
$promo_img_values = field_get_items('node', $node, 'field_promo_image');
if (!$promo_img_values) $promo_img_values = field_get_items('node', $node, 'field_image_gallery');
if ($promo_img_values) {
    $promo_img = image_style_url('news_teaser', $promo_img_values[0]['uri']);
}
// информация о записи в заголовке
$user_name = person_get_user_name($node->uid);
$vcount = statistics_get($node->nid);
?>

<article class="blog teaser contextual-links-region"<?php print $attributes; ?>>
    <!-- если тизер выводится в результатах поиска - добавить заголовок с названием типа-->
    <?php if ($_GET['q'] == 'search'): ?>
        <div class="type"><?php print t('Blog'); ?></div>
    <?php endif; ?>

    <div class="content" property="content:encoded">
        <header>
            <h2 property="dc:title" class="title">
                <a href="<?php print $node_url; ?>"><?php print $title; ?></a>
            </h2>
            <div class="post-info">
                <div><a href="/person/<?php print $node->uid; ?>/summary" title="Информация о пользователе." class="username" xml:lang="" about="/person/<?php print $node->uid; ?>/summary" typeof="sioc:UserAccount" property="foaf:name" datatype=""><?php print $user_name; ?></a></div>
                <div><?php print format_date($node->created, 'custom', 'd.m.Y'); ?></div>
                <div><? print format_plural($vcount['totalcount'], '@count view', '@count views'); ?></div>
                <div><? print format_plural($node->comment_count, '@count comment', '@count comments'); ?></div>
            </div>
        </header>
            <div class="img-wrap">
                <?php if ($promo_img_values): ?>
                <a href="<?php print $node_url; ?>" class="promo-image">
                    <img src="<?php print $promo_img; ?>" alt="<?php print $node->title; ?>" property="dc:image">
                </a>
                <?php endif; ?>
            </div>

        <div class="text-wrap">
            <?php print $content['body'][0]['#markup']; ?>
            <div class="read-more"><?php print $content['node_link'][0]['#markup']; ?></div>
        </div>
        <?php print views_embed_view('blog_tags', $display_id = 'default', $node->nid); ?>
    </div>
</article>

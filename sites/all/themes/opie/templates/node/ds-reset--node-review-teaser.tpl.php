<?php
// Promo-image.
$promo_img_values = field_get_items('node', $node, 'field_promo_image');
if ($promo_img_values) {
    $promo_img = image_style_url('news_teaser', $promo_img_values[0]['uri']);
}

// информация о записи в заголовке
$account = user_load($node->uid);
$user_name = person_get_user_name($node->uid);
$vcount = statistics_get($node->nid);
$blogger_company = '';
$blogger_post = 'Пользователь';
    // если утвержденный сотрудник вывести регалии
if ($staff = profile2_load_by_user($account, 'staff')) {
    $staff_wrapper = entity_metadata_wrapper('profile2', $staff);

    if ($staff_wrapper->field_profile_company_approved->value() == 1) {
        $blogger_company = person_get_user_company_name($staff_wrapper->field_profile_company2->pid->value());
        $blogger_post = $staff_wrapper->field_profile_post->value();
    }
}
?>

<article class="review teaser contextual-links-region"<?php print $attributes; ?>>
    <!-- если тизер выводится в результатах поиска - добавить заголовок с названием типа-->
    <?php if ($_GET['q'] == 'search'): ?>
        <div class="type"><?php print 'Отзыв о продукции'; ?></div>
    <?php endif; ?>

    <div class="content" property="content:encoded">
        <header>
            <h2 property="dc:title" class="title">
                <a href="<?php print $node_url; ?>"><?php print $title; ?></a>
            </h2>
            <div class="post-info">
                <div><a href="/person/<?php print $node->uid; ?>/summary" title="Информация о пользователе." class="username" xml:lang="" about="/person/<?php print $node->uid; ?>/summary" typeof="sioc:UserAccount" property="foaf:name" datatype=""><?php print $user_name; ?></a></div>
                <div><? print $blogger_post . ' ' . $blogger_company; ?></div>
                <div><?php print format_date($node->created, 'custom', 'd.m.Y'); ?></div>
                <div><? print format_plural($vcount['totalcount'], '@count view', '@count views'); ?></div>
            </div>
        </header>
        <?php if ($promo_img_values): ?>
            <div class="img-wrap">
                <a href="<?php print $node_url; ?>" class="promo-image">
                    <img src="<?php print $promo_img; ?>" alt="<?php print $node->title; ?>" property="dc:image">
                </a>
            </div>
        <?php endif; ?>

        <div class="text-wrap">
            <? if (!empty($content['field_review_intro']['#items'][0]['safe_value'])): ?>
                <h2><?php print $content['field_review_intro']['#items'][0]['safe_value']; ?></h2>
            <? endif; ?>
            <?php print $content['body'][0]['#markup']; ?>
            <div class="read-more"><?php print $content['node_link'][0]['#markup']; ?></div>
        </div>
    </div>
</article>

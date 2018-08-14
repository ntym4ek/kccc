<article class="information full" <?php print $attributes; ?>>
    <!-- если тизер выводится в результатах поиска - добавить заголовок с названием типа-->
    <?php if ($_GET['q'] == 'search'): ?>
        <div class="type"><?php print t('Information'); ?></div>
    <?php endif; ?>

    <div class="content" property="content:encoded">
        <?php print render($content['body']); ?>
    </div>
</article>
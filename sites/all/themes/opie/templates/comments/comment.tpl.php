<?php
?>
<article class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

    <div class="img-wrap">
        <?php print $picture; ?>
    </div>

    <div class="text-wrap">
            <p class="submitted">
              <?php print $submitted; ?>
              <?php print $permalink; ?>
            </p>

            <?php print render($title_prefix); ?>
<!--            --><?php //if ($title): ?>
            <?php if (false): ?>
              <h3<?php print $title_attributes; ?>>
                <?php print $title; ?>
                <?php if ($new): ?>
                  <mark class="new"><?php print $new; ?></mark>
                <?php endif; ?>
              </h3>
            <?php elseif ($new): ?>
              <mark class="new"><?php print $new; ?></mark>
            <?php endif; ?>
            <?php print render($title_suffix); ?>

            <?php if ($status == 'comment-unpublished'): ?>
              <mark class="unpublished"><?php print t('Unpublished'); ?></mark>
            <?php endif; ?>

        <?php
            // We hide the comments and links now so that we can render them later.
            hide($content['links']);
            print render($content);
        ?>

        <?php if ($signature): ?>
            <footer class="user-signature clearfix">
                <?php print $signature; ?>
            </footer>
        <?php endif; ?>

        <?php print render($content['links']) ?>
    </div>
</article>

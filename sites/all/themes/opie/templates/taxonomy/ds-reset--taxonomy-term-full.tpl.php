<?php

?>
<article class="grid-full taxonomy full"<?php print $attributes; ?>>
    <div class="grid-1-3 image-wrap left">
        <?php print render($content['field_promo_image']); ?>
    </div>
    <div class="text-wrap" property="content:encoded">
        <?php print $description; ?>
    </div>
</article>
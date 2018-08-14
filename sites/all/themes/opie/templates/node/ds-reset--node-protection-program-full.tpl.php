<?php
$pp = get_protection_program($elements['#node']->nid);
$table = theme('protection_program_table', array('pp' => $pp));
?>

<article class="protection-program full" <?php print $attributes; ?>>
    <?php print $table;?>
</article>
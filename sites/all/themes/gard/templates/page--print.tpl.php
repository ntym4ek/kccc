<?php
// если страница персонала, не выводить верхнее меню, футер и проч.

global $user;
?>
<div class="content-container landing">
    <div class="content">
        <?php print render($page['content']); ?>
  </div>
</div>  


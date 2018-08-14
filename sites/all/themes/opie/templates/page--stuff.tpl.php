<?php
// если страница персонала, не выводить верхнее меню, футер и проч.

global $user;
?>
<div id="page">
  <div id="page-wrapper">   
    <div id="content-container">
        <?php print render($page['content_stuff']); ?>
    </div>
  </div>
</div>  
<div id="bg1"></div>

<?php
?>

<div class="card card-reglament">
  <div class="content">
    <div class="decor-line"></div>
    <div class="decor-circle"></div>

    <div class="cultures h4">
      <?php print $card['cultures']['formatted']; ?>
    </div>

    <div class="hobjects">
      <?php print $card['hobjects']['formatted']; ?>
    </div>

    <div class="row">
      <div class="col-xs-6">
        <div class="b">
          <div class="title">норма применения</div>
          <div class="text"><?php print $card['prep_spends']['formatted']; ?></div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="b">
          <div class="title">расход рабочей жидкости</div>
          <div class="text"><?php print $card['mix_spend']['formatted']; ?></div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-6">
        <div class="b">
          <div class="title">срок ожидания </div>
          <div class="text"><?php print $card['wait']['formatted']; ?></div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="b">
          <div class="title">кратность обработок</div>
          <div class="text"><?php print $card['ratio']['formatted']; ?></div>
        </div>
      </div>
    </div>
    <?php if ($card['description']): ?>
    <div class="row">
      <div class="col-xs-12">
        <div class="b">
          <div class="title">способ, сроки применения </div>
          <div class="description"><?php print $card['description']; ?></div>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>


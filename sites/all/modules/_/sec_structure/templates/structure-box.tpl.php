<div class="box-wrapper<? print (empty($element['class']) ? '':' '.$element['class']); ?>">
  <div class="box">
    <div class="box-shell">
      <? print $element['shell']; ?>
      <? if (!empty($element['photo'])): ?>
        <div class="box-photo"><img src="<? print $element['photo']; ?>" /></div>
      <? endif; ?>
    </div>
    <div class="box-card<? print (empty($element['card']) ? ' empty' : ''); ?>">
      <div class="box-content">
        <? print $element['card']; ?>
      </div>
    </div>
    <? if (!empty($element['level'])): ?>
      <div class="box-trace"></div>
    <? endif; ?>
  </div>
  <? if (!empty($element['level'])): ?>
  <div class="box-sublevel">
    <? print render($element['level']); ?>
  </div>
  <? endif; ?>

</div>



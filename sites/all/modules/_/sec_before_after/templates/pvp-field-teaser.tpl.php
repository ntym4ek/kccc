<?php
?>
<div class="pvp-field pvp-u-list-teaser">
  <?php if (empty($field)): ?>
    <div class="pvp-add-field">
      <a href="/fields/add">Добавить поле</a>
    </div>
  <?php else: ?>
    <div class="pvp-t-header">
      <div class="pvp-t-title">
        <a href="/field/<?php print $field['id']; ?>/season/list">
          <div class="title-1"><?php print $field['label']; ?></div>
          <div class="title-2"><?php print $field['farm'] . ', ' . $field['region']['label']; ?></div>
        </a>
      </div>
      <?php if (user_has_role(ROLE_ADMIN) || user_has_role(ROLE_STAFF_EDITOR)): ?>
        <div class="pvp-t-author">
          <img src="<?php print $field['author']['photo']; ?>" alt="">
          <div><?php print $field['author']['short_name']; ?></div>
        </div>
      <?php endif; ?>
      <div class="pvp-t-actions">
        <a href="/fields/edit/<?php print $field['id']; ?>" class="action-edit">
          <i class="fas fa-pencil-alt" aria-hidden="true"></i>
        </a>
        <a href="/fields/del/<?php print $field['id']; ?>" class="action-delete">
          <i class="fa fa-trash" aria-hidden="true"></i>
        </a>
      </div>
    </div>
    <div class="pvp-field-seasons">
      <?php if (!empty($field['seasons'])): ?>
        <?php foreach($field['seasons'] as $season): ?>
          <div class="pvp-field-season">
            <a href="/season/<?php print $season['id']; ?>/processing/list">
              <div class="pvp-s-year"><?php print $season['year']; ?></div>
              <div class="pvp-s-culture"><?php print $season['culture']['label']; ?></div>
            </a>
            <div class="pvp-published"><a href="#" title="<?php print $season['publication']['published'] ? 'Опубликовано' : 'Не опубликовано'; ?>"><?php print $season['publication']['published'] ? '<i class="fas fa-check"></i>' : '<i class="fas fa-exclamation-circle"></i>'; ?></a></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
      <div class="pvp-field-season">
        <a href="/field/<?php print $field['id']; ?>/season/add">Добавить<br>сезон</a>
      </div>
    </div>
  <?php endif; ?>
</div>

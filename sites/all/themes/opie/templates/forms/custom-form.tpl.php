<?
/**
 * универсальная форма
 */



?>
<div class="grouped-form">

    <? foreach($form as $key_f => $element): ?>
        <? if (isset($element['#type']) && $element['#type'] == 'container'): ?>
        <div class="group-<? print $key_f; ?> form-wrapper form-group" id="edit-group-<? print $key_f; ?>">
            <div class="group-header">
                <h2><? print $element['#title']; ?></h2>
                <div class="group-help"></div>
            </div>
            <? foreach($element as $key_e => $sub_element): ?>
                <? if (isset($sub_element['#type']) && in_array($sub_element['#type'], array('textfield', 'textarea', 'select', 'item'))): ?>
                    <?php print render($form[$key_f][$key_e]); ?>
                <? endif; ?>
            <? endforeach; ?>
        </div>
        <? endif; ?>
    <? endforeach; ?>


    <?php echo drupal_render_children($form)?>
</div>
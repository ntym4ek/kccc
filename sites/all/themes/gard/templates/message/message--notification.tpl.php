<?php

/**
 * @file
 * Default theme implementation for message entities.
 *
 * Available variables:
 * - $content: An array of comment items. Use render($content) to print them all, or
 *   print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $title: The (sanitized) entity label.
 * - $url: Direct url of the current entity if specified.
 * - $page: Flag for the full page state.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. By default the following classes are available, where
 *   the parts enclosed by {} are replaced by the appropriate values:
 *   - entity-{ENTITY_TYPE}
 *   - {ENTITY_TYPE}-{BUNDLE}
 *
 * Other variables:
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 *
 * @see template_preprocess()
 * @see template_preprocess_message()
 * @see template_process()
 */
?>

<div class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
    <div class="main">
        <div class="m-header">
            <div class="line-item"><?php print render($content['message__message_text__4']); ?></div>
            <div class="line-item m-date"><i class="fas fa-clock"></i><?php print date('d.m.Y - H:i', $message->timestamp); ?></div>
        </div>
        <div class="m-content"><?php print render($content['message__message_text__2']); ?></div>
        <div class="m-footer"><?php print render($content['message__message_text__3']); ?></div>
    </div>
    <div class="actions">
        <? if ($is_message_got): ?>
            <a href="/message/%/ungot" title="<? print t('Set notification read'); ?>" class="m-got"><i class="fas fa-check"></i></a>
        <? else: ?>
            <a href="/message/%/got" title="<? print t('Set notification read'); ?>" class="m-ungot"><i class="fas fa-check"></i></a>
        <? endif; ?>
    </div>
</div>
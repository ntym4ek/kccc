<?php

/**
 * Returns HTML for a select form element.
 *
 * It is possible to group options together; to do this, change the format of
 * $options to an associative array in which the keys are group labels, and the
 * values are associative arrays in the normal $options format.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #value, #options, #description, #extra,
 *     #multiple, #required, #name, #attributes, #size.
 *
 * @ingroup themeable
 */
function gard_select($variables) {
    $element = $variables['element'];
    element_set_attributes($element, array('id', 'name', 'size'));
    _form_set_class($element, array('form-select'));

    return '<select' . drupal_attributes($element['#attributes']) . '>' . gard_form_select_options($element) . '</select>';
}

/**
 * Converts an array of options into HTML, for use in select list form elements.
 *
 * This function calls itself recursively to obtain the values for each optgroup
 * within the list of options and when the function encounters an object with
 * an 'options' property inside $element['#options'].
 *
 * @param array $element
 *   An associative array containing the following key-value pairs:
 *   - #multiple: Optional Boolean indicating if the user may select more than
 *     one item.
 *   - #options: An associative array of options to render as HTML. Each array
 *     value can be a string, an array, or an object with an 'option' property:
 *     - A string or integer key whose value is a translated string is
 *       interpreted as a single HTML option element. Do not use placeholders
 *       that sanitize data: doing so will lead to double-escaping. Note that
 *       the key will be visible in the HTML and could be modified by malicious
 *       users, so don't put sensitive information in it.
 *     - A translated string key whose value is an array indicates a group of
 *       options. The translated string is used as the label attribute for the
 *       optgroup. Do not use placeholders to sanitize data: doing so will lead
 *       to double-escaping. The array should contain the options you wish to
 *       group and should follow the syntax of $element['#options'].
 *     - If the function encounters a string or integer key whose value is an
 *       object with an 'option' property, the key is ignored, the contents of
 *       the option property are interpreted as $element['#options'], and the
 *       resulting HTML is added to the output.
 *   - #value: Optional integer, string, or array representing which option(s)
 *     to pre-select when the list is first displayed. The integer or string
 *     must match the key of an option in the '#options' list. If '#multiple' is
 *     TRUE, this can be an array of integers or strings.
 * @param array|null $choices
 *   (optional) Either an associative array of options in the same format as
 *   $element['#options'] above, or NULL. This parameter is only used internally
 *   and is not intended to be passed in to the initial function call.
 *
 * @return string
 *   An HTML string of options and optgroups for use in a select form element.
 */
function gard_form_select_options($element, $choices = NULL) {
    if (!isset($choices)) {
        $choices = $element['#options'];
    }
    // array_key_exists() accommodates the rare event where $element['#value'] is NULL.
    // isset() fails in this situation.
    $value_valid = isset($element['#value']) || array_key_exists('#value', $element);
    $value_is_array = $value_valid && is_array($element['#value']);
    $options = '';
    foreach ($choices as $key => $choice) {
        if (is_array($choice)) {
            $options .= '<optgroup label="' . check_plain($key) . '">';
            $options .= form_select_options($element, $choice);
            $options .= '</optgroup>';
        }
        elseif (is_object($choice)) {
            $options .= form_select_options($element, $choice->option);
        }
        else {
            $key = (string) $key;
            if ($value_valid && (!$value_is_array && (string) $element['#value'] === $key || ($value_is_array && in_array($key, $element['#value'])))) {
                $selected = ' selected="selected"';
            }
            else {
                $selected = '';
            }
            // custom -> commenting options (with key '') can't be chosen by user
            $disabled = ($key === '') ? ' disabled="disabled"' : '';

            $options .= '<option value="' . check_plain($key) . '"' . $selected . $disabled . '>' . check_plain($choice) . '</option>';
        }
    }
    return $options;
}
<?php

/**
 * @file
 * Definition of views_handler_argument_numeric.
 */

/**
 * Basic argument handler for arguments that are numeric.
 *
 * Incorporates break_phrase.
 *
 * @ingroup views_argument_handlers
 */
class ext_message_got_views_handler_argument_timestamp extends views_handler_argument {

    /**
     * {@inheritdoc}
     */
    public function option_definition() {
        $options = parent::option_definition();
        // объявление опций в дополнительной секции
        $options['not'] = array('default' => FALSE, 'bool' => TRUE);

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function options_form(&$form, &$form_state) {
        parent::options_form($form, $form_state);
        // опции в дополнительной секции
        // инверсия
        $form['not'] = array(
            '#type' => 'checkbox',
            '#title' => t('Exclude'),
            '#description' => t('If selected, the numbers entered for the filter will be excluded rather than limiting the view.'),
            '#default_value' => !empty($this->options['not']),
            '#fieldset' => 'more',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function query($group_by = FALSE)
    {
        $this->ensure_my_table();
        // формируем запрос timestamp ==/!= NULL
        // чтобы определить наличие записи о прочтении
        $operator = empty($this->argument) ? true : false;
        $operator = empty($this->options['not']) ? $operator : !$operator;
        $operator = $operator ? '' : ' NOT';
        $this->query->add_where_expression(0, "$this->table_alias.$this->real_field IS$operator NULL");
    }

}

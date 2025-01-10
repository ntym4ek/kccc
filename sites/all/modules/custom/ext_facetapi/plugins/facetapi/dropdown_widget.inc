<?php

/**
 * Widget that renders facets as a list of clickable checkboxes.
 *
 * This widget renders facets in the same way as FacetapiWidgetLinks but uses
 * JavaScript to transform the links into checkboxes followed by the facet.
 */
class FacetapiWidgetDropdownsCheckboxLinks extends FacetapiAjaxWidgetCheckboxes {

  /**
   * Overrides FacetapiWidgetLinks::init().
   *
   * Adds additional JavaScript settings and CSS.
   */
  public function init() {
    parent::init();
    // Add dropdown filters.
    if (isset($this->settings->settings['filter']) && $this->settings->settings['filter']) {
      $this->build['#attached']['js'][] = drupal_get_path('module', 'ext_facetapi') . '/js/diacritics.js';
      $this->build['#attached']['js'][] = drupal_get_path('module', 'ext_facetapi') . '/js/dropdown-filter.js';
      $this->build['#attributes']['class'][] = 'dropdown-filter panel-body';
    }
  }

  /**
   * Returns uuid for the facet widget.
   * It's need to identify the facet option.
   * @param null $value
   *   Some facets have many separate input elements. For example checkboxes or links.
   *   We need $value to identify each option.
   */
  protected function getAjaxFacetsUuid($value = NULL) {
    $to_replace = ['_', ' ', ':'];
    switch ($this->id) {
      case 'facetapi_ajax_select':
      case 'facetapi_ajax_ranges':
        return str_replace('_', '-', $this->id) . '-' . str_replace($to_replace, '-', $this->key);
        break;

      case 'facetapi_ajax_links':
      case 'facetapi_ajax_checkboxes':
      case 'facetapi_dropdowns_ajax_checkboxes':
        return str_replace('_', '-', $this->id) . '-' . str_replace($to_replace, '-', $this->key . '-' . drupal_strtolower($value));
        break;
    }
  }

  /**
   * Implements FacetapiWidget::execute().
   *
   * Transforms the render array into something that can be themed by
   * theme_item_list().
   *
   * @see FacetapiWidgetLinks::setThemeHooks()
   * @see FacetapiWidgetLinks::buildListItems()
   */
  public function execute() {
    $element = &$this->build[$this->facet['field alias']];

    // сменить название виджета на 'facetapi_ajax_checkboxes'
    // так как js обрабчик навешивается на виджеты по имени
    $this->jsSettings["widget"] = 'facetapi_ajax_checkboxes';

    // класс для списка, по которому скрипт добавляет поле ввода
    $this->build['#attributes']['class'][] = 'dropdown-menu';

    // Sets each item's theme hook, builds item list.
    $this->setThemeHooks($element);
    $item_list = [
      '#theme' => 'item_list',
      '#items' => $this->buildListItems($element),
      '#attributes' => $this->build['#attributes'],
    ];

    // We cannot use drupal_html_id to save the same id for each facet.
    $wrapper_id = $this->build['#attributes']['id'] . '-wrapper';

    $element = [
      '#markup' => '<div id="' . $wrapper_id . '" class="dropdown dropdown-more">'
        . $this->getResetLink()
        . render($item_list)
        . '</div>'
    ];

    ajax_facets_add_ajax_js($this->facet);
  }

  /**
   * Overrides FacetapiWidget::settingsForm().
   */
  public function settingsForm(&$form, &$form_state) {
    parent::settingsForm($form, $form_state);

    $states = array(
      'visible' => array(
        'select[name="widget"]' => array('value' => $this->id),
      ),
    );

    $form['widget']['widget_settings']['links'][$this->id]['filter'] = array(
      '#title' => t('Filter Facet.'),
      '#type' => 'checkbox',
      '#default_value' => !empty($this->settings->settings['filter']) ? $this->settings->settings['filter'] : '',
      '#states' => $states,
    );
  }

}

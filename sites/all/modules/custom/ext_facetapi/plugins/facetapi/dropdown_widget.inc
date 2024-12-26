<?php

/**
 * Widget that renders facets as a list of clickable checkboxes.
 *
 * This widget renders facets in the same way as FacetapiWidgetLinks but uses
 * JavaScript to transform the links into checkboxes followed by the facet.
 */
class FacetapiWidgetDropdownsCheckboxLinks extends FacetapiWidgetCheckboxLinks {

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

    // Sets each item's theme hook, builds item list.
    $this->setThemeHooks($element);
    $element = array(
      '#theme' => 'dropdown_facet',
      '#items' => $this->buildListItems($element),
      '#attributes' => $this->build['#attributes'],
    );
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

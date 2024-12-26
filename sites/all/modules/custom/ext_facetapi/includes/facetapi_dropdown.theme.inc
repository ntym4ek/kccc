<?php

/**
 * @file
 * Theming function for facetapi bootstrap dropdown.
 */

/**
 * Extend theme_item_list(),.
 */

/**
 * Display facet link with checkbox  in bootstrap dropdown menu.
 */
function theme_dropdown_facet(&$vars) {
  $items = $vars['element']['#items'];
  $attributes = $vars['element']['#attributes'];
  // Add Dropdown attributes to items.
  $attributes['class'][] = 'dropdown-menu';
  // Only output the list container and title, if there are any list items.
  // Check to see whether the block title exists before adding a header.
  // Empty headers are not semantic and present accessibility challenges.
  $output = '<div class="dropdown dropdown-more">';

  if (!empty($items)) {
    $output .= "<ul" . drupal_attributes($attributes) . '>';
    $num_items = count($items);
    $i = 0;
    foreach ($items as $item) {
      $attributes = array();
      $children = array();
      $data = '';
      $i++;
      if (is_array($item)) {
        foreach ($item as $key => $value) {
          if ($key == 'data') {
            $data = $value;
          }
          elseif ($key == 'children') {
            $children = $value;
          }
          else {
            $attributes[$key] = $value;
          }
        }
      }
      else {
        $data = $item;
      }
      // Type of the list to return - ul or ol.
      $type = 'ul';
      if (count($children) > 0) {
        // Render nested list.
        $data .= theme_item_list(array(
          'items' => $children,
          'title' => NULL,
          'type' => $type,
          'attributes' => $attributes,
        ));
      }
      if ($i == 1) {
        $attributes['class'][] = 'first';
      }
      if ($i == $num_items) {
        $attributes['class'][] = 'last';
      }
      $attributes['class'][] = 'list-group-item';
      $output .= '<li' . drupal_attributes($attributes) . '><div class="form-item form-type-checkbox">' . $data . "</div></li>\n";
    }
    $output .= "</ul>";
  }
  $output .= '</div>';
  return $output;
}

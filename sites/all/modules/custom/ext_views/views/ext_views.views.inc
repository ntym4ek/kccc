<?php

/**
 * Implements of hook_views_data().
 */
function ext_views_views_data()
{
  return array(
    'node' => array(
      'recent_filter' => array(
        'group' => t('Custom'),
        'title' => t('Previous materials'),
        'help' => t('Selects only previous nodes, which creation date is less than current\'s (from URL) node'),
        'filter' => array(
          'handler' => 'ext_views_handler_filter_recent_filter',
        ),
      )
    )
  );
}

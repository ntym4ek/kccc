<?php

/**
 * My custom filter handler
 */
class ext_views_handler_filter_recent_filter extends views_handler_filter
{
  function query() {
    $this->ensure_my_table();

    // не получилось найти и настроить контекстный фильтр для вывода предыдущих новостей
    // поэтому написан этот фильтр, добавляющий сравнение
    // todo Есть возможность написать свой контекстный фильтр?
    if (arg(0) == 'node' && is_numeric(arg(1))) {
      $current_node = node_load(arg(1));
      $this->query->add_where_expression($this->options['group'], "{$this->table_alias}.created < {$current_node->created}");
    }
  }
}

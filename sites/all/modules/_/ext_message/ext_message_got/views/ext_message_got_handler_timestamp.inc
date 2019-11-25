<?php

// Определяем обработчик, наследуем от стандартного класса "числовое значение"
class ext_message_got_handler_timestamp extends views_handler_field_date
{
    // Запрос к БД
    function query() {
        // Определяем таблицу
        $table = $this->query->ensure_table('message_got');
        // Запрашиваем наличие записи о прочтении Сообщения
        $sql = "SELECT mg.timestamp FROM {message_got} mg WHERE mg.mid = message.mid AND mg.uid = " . $GLOBALS['user']->uid;
        // Указываем название для возвращаемого поля
        $this->query->add_field('', "($sql)", 'timestamp');
        $this->field_alias = 'timestamp';
    }
}

Модуль позволяет пользователям подписываться на уведомления о событиях на сайте.
Для этого создаются: 
1)  словарь со списком подписок;
2)  флаг, отвечающий за подписку на уведомления из этого списка;
3)  типы сообщения, отвечающие за хранение информации о событиях на сайте;
4)  плагины, отвечающие за отправку уведомлений о событиях

ДОБАВЛЕНИЕ СОБЫТИЯ 
==================
1)  Добавить новую константу для подписки в список _ext_message_get_vars() ext_message.module.
2)  Добавить новый тип сообщения в ext_message.install
3)  Добавить создание сообщения нового типа и отправку уведомления в качестве реакции 
    на нужное событие в ext_message.module
    
TODO
====
1)  Вывод на сайте уведомлений на которые подписаны пользователи
2)  Регистрацию просмотра пользователями этих уведомлений
3)  Вывод в приложении
4)  Плагин пуш уведомлений
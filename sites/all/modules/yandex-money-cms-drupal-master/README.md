##yanexmoney-drupal-commerce-ubercart

Модуль оплаты yanexmoney-drupal-commerce-ubercart необходим для интеграции с сервисом [Яндекс.Касса](http://kassa.yandex.ru/) на базе CMS Drupal и компонента [Drupal commerce](https://www.drupal.org/project/commerce) или [Drupal ubercart](https://www.drupal.org/project/ubercart).

 Доступные платежные методы, если вы работаете как юридические лицо:
* **Банковские карты** -  Visa (включая Electron), MasterCard и Maestro любого банка мира
* **Электронные деньги** - Яндекс.Деньги и WebMoney
* **Наличные** - [Более 170 тысяч пунктов](https://money.yandex.ru/pay/doc.xml?id=526209) оплаты по России
* **Баланс телефона** - Билайн, МегаФон и МТС
* **Интернет банкинг** - Альфа-Клик и Сбербанк Онлайн

###Требования к CMS Drupal:
* версия 7;
* компонент commerce или ubercart.

###Установка модуля
Для установки данного модуля необходимо распаковать содержимое [архива](https://github.com/yandex-money/yandex-money-cms-drupal/archive/master.zip) в папку `/sites/all/modules` вашего сайта и активировать модули из административной панели.
Далее рекомендуем следовать пунктам [инструкции](http://kassa.yandex.ru/files/drupal.pdf).

Внимание! Модуль еще не тестировался с Ubercart. Необходима доработка модуля Donate (сейчас не работает).

Пожалуйста, обязательно делайте бекапы!

Примечание. Для перевода интерфейса модуля на русский язык необходимо активировать модуль `Locale` и импортировать файл `ru-ru.po` через меню `Конфигурация`-`Перевод интерфейса`- вкладка `Импорт`.

###Список дел
- [ ] Test Ubercart integration
- [ ] Fix Donate module

###Нашли ошибку или у вас есть предложение по улучшению модуля?
Пишите нам cms@yamoney.ru
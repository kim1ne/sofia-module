# sofia-module

Устанавливается через Marketplace

## при установке
создаётся агент, который раз в сутки запускает проверку новостей без авторов
```php
\Sofia\Test\News\Agents\CheckNewsNotUser::class
```

Регистрируется обработчик события OnProlog
```php
\Sofia\Test\News\Event\OnProlog::class
```
который регистрирует события:
```php
[\Sofia\Test\News\Event\Orm\News::class, 'onAfterAdd'],
[\Sofia\Test\News\Event\Orm\News::class, 'onAfterUpdate'],
```
Создаётся таблица
```php
\Sofia\Test\News\Orm\NewsTable::class
```

Копируется компонент в папку local/components
копируется файл для меню в админку в папку bitrix/admin
sofia.test.news/admin/sofia_test_new.php

При удалении обрабтное действие

## Как пользоваться
в админке доступно меню в разделе "Настройки" (страница новостей)
по адресу /bitrix/admin/sofia_test_new.php?page=1

Можно создавать, редактировать и удалять новости

Страница переключается с помощью кнопок и GET-параметра page

Количество записей Указывается настройкой в компоненте, в нём же можно и сортировать дату
параметры компонента:
PAGE_SIZE - размер страницы
FILTER_DATE - Дата
DATE_DIRECTION - направление даты > <

при создании, обновлении вызывается обработчик с одноимёнными методами
```php
\Sofia\Test\News\Event\Orm\News::class
```
Он отправляет сообщение администратору сайта

## Агент
```php
\Sofia\Test\News\Agents\CheckNewsNotUser::class
```
Получает все записи, без авторов и отправляет на Email администратору

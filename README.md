# Bitrix Extensions

Модуль содержит в себе различного рода классы для облегчения непростой жизни разработчика сайтов под 1С-Битрикс.

## Установка:

1) `composer require itgro/extensions`

2) Для работы мигаций, необходимо выполнить инструкции по установке с **[arrilot/bitrix-migrations](https://github.com/arrilot/bitrix-migrations)**

3) Для корректной работы twig-шаблонов, необходимо выполнить инструкцию по настройке с **[maximaster/tools.twig](https://github.com/maximaster/tools.twig/blob/master/docs/configuration.md)**

4) Для работы ajax-роутера необходимо добавить в `urlrewrite.php` (желательно повыше):
```php
array(
    'CONDITION' => '#^\/ajax\/([^\/]*)#',
    'RULE' => 'handler=$1',
    'ID' => '',
    'PATH' => '/ajax/index.php',
),
```

## Какие штуки имеются:

* `\Itgro\BufferView` - работа с `$APPLICATION->ShowViewContent()`, но в более узком и понятном круге;

* `\Itgro\Mail` - отправитель писем как через шаблоны Битрикс, так и через обычную отправку;

* `\Itgro\Entity\IBlock\Base` - абстрактный класс для более удобной работы с `CIBlockElement`

* `\Itgro\Entity\IBlock\Entity` - абстрактный класс (с предыдущим в качестве родительского), позволяющий каждый элемент возвращаемого массива использовать как объект

* Возможность добавить свои ajax-обработчики посредством навешивания обработчиков на `\Itgro\Ajax\Distributor::EXPAND_HANDLERS_EVENT`:
```php
/**
 * В этом примере будут доступны запросы вида `/ajax/feedback/%method%/`,
 * каждый из которых будет делигироваться в соответствующий класс на соответствующий метод
 */
event_manager()->addEventHandler('extensions', \Itgro\Ajax\Distributor::EXPAND_HANDLERS_EVENT, function (\Bitrix\Main\Event $event) {
    return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, [
        'feedback' => \Namespace\Some\Class::class,
    ]);
})
``` 

* Возможность добавить свои функции для Twig'а посредством навешивания обработчиков на `\Itgro\Twig\Extension\Functions::EXPAND_HANDLERS_EVENT`:
```php
event_manager()->addEventHandler('extensions', \Itgro\Twig\Extension\Functions::CREATE_HANDLERS_LIST_EVENT, function (\Bitrix\Main\Event $event) {
    return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, [
        'foo_func' => ['\Namespace\Some\Class::class', 'fooFunc'],
        'bar_func' => ['\Namespace\Some\Second\Class::class', 'barFunc'],
    ]);
})
``` 

* Возможность добавить свои фильтры для Twig'а посредством навешивания обработчиков на `\Itgro\Twig\Extension\Filters::EXPAND_HANDLERS_EVENT`. Код будет выглядеть примерно как в коде выше.

# Bitrix Extensions

Модуль содержит в себе различного рода классы для облегчения непростой жизни разработчика сайтов под 1С-Битрикс.

## Установка:

1) `composer require itgro/extensions`

2) Для работы мигаций, необходимо выполнить инструкции по установке с **[arrilot/bitrix-migrations](https://github.com/arrilot/bitrix-migrations)**

3) Для корректной работы twig-шаблонов, необходимо выполнить инструкцию по настройке с **[maximaster/tools.twig](https://github.com/maximaster/tools.twig/blob/master/docs/configuration.md)**

4) Для работы ajax-роутера необходимо добавить в `urlrewrite.php` (желательно повыше):
```
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



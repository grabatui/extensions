# Bitrix Extensions

Модуль содержит в себе различного рода классы для облегчения непростой жизни разработчика сайтов под 1С-Битрикс.

## Установка:

1) `composer require itgro/extensions`

2) Для корректной работы twig-шаблонов, необходимо выполнить инструкцию по настройке с **[maximaster/tools.twig](https://github.com/maximaster/tools.twig/blob/master/docs/configuration.md)**

3) Для работы ajax-роутера необходимо добавить в `urlrewrite.php` (желательно повыше):
    ```php
    array(
        'CONDITION' => '#^\/ajax\/([^\/]*)#',
        'RULE' => 'handler=$1',
        'ID' => '',
        'PATH' => '/ajax/index.php',
    ),
    ```

4) Для работы динамических агентов необходимо добавить инициализацию ядра агентов посредством `(new \Itgro\Cron\Kernel)->register();`.
*Внимание!* Сделать это необходимо после создания обработчика для регистрации агентов

## Какие штуки имеются:

#### Классы-помощники:

* `\Itgro\BufferView` - работа с `$APPLICATION->ShowViewContent()`, но в более узком и понятном круге;

* `\Itgro\Log` - небольшая обёртка для `CEventLog::Log()`;

* `\Itgro\Mail` - отправитель писем как через шаблоны Битрикс, так и через обычную отправку;

* `\Itgro\Router` - класс для возможности хранить все (более-менее простые) ссылки проекта в одном массиве и доставать их через короткий хэлпер. По умолчанию все алиасы хранятся в корне сайта в файле `routes.php`. Путь до этого файла (от корня сайта) можно изменить, объявив константу `EXTENSIONS_ROUTES_PATH`. Массив в `routes.php` имеет очень простую структуру вида:
    ```php
    return [
        'auth' => '/personal/auth/',
        'register' => '/personal/registration/',
    ];
    ```
Т.о. можно через `\Itgro\Router::getByCode('auth')` (или хэлпер `route('auth')`) достать нужный путь;

* `\Itgro\Session` - обёртка для работы с сессией (все данные хранятся не в корне `$_SESSION`, а в подмассиве);

* По namespace'у `\Itgro\Bitrx\*` есть кучка различных классов, позволяющих доставать простые значение (аля "Дай id по коду").
<br>Все данные кешируются. Т.о. если вы через `\Itgro\Bitrix\IBlock\Iblock::getByCode()` достаните один раз id ИБ, то все последующие вызовы будут обращаться к уже закешированному значению.

* `\Itgro\Sharing` - класс (и обработчики рядом) для создания ссылок для шаринга в соц.сети.

#### Классы для работы с сущностями Битрикса:

* `\Itgro\Entity\IBlock\Base` - абстрактный класс для более удобной работы с `CIBlockElement`;

* `\Itgro\Entity\IBlock\Entity` - абстрактный класс (с предыдущим в качестве родительского), позволяющий каждый элемент возвращаемого массива использовать как объект;

* В `\Itgro\Entity\IBlock\Base` (и, соответственно, в `\Itgro\Entity\IBlock\Entity`) имеется трейт `\Bitrix\Entity\IBlock\WithEvents` (который Вы, естественно, можете использовать и в каких-нибудь других типах Битрикс-сущностей).
<br>Он позволяет вешать обработчики (обновление, добавление, удаление элемента) на конкретные Ваши сущности, чтобы обработчик срабатывал исключительно для определённого ИБ.
<br>Ваши сущности, конечно, можно расширять на новые обработчики (например, "При активации", которая расширяет `\Bitrix\Entity\IBlock\WithEvents::afterUpdate()`);

* `\Itgro\Entity\HighloadIBlock\Base` - абстрактный класс для удобной работы с Highload-ИБ;

* `\Itgro\Entity\DataManager\Base` - абстрактный класс для удобной работы с отдельными таблицами, для которых есть свой ORM-класс.

#### Расширители классов:

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
    Или даже проще:
    ```php
    \Itgro\Ajax\Distributor::setHandlers([
        'feedback' =>\Namespace\Some\Class::class,
    ]);
    ```
    Для универсальности данных от ajax-запросов, имеются классы по namespace'у `\Itgro\Ajax\Result\*`. Их можно возвращать в виде ответов ajax-методов и обрабатывать по типу возвращаемого объекта.

* Возможность добавить свои функции для Twig'а посредством навешивания обработчиков на `\Itgro\Twig\Extension\Functions::EXPAND_HANDLERS_EVENT`:
    ```php
    event_manager()->addEventHandler('extensions', \Itgro\Twig\Extension\Functions::CREATE_HANDLERS_LIST_EVENT, function (\Bitrix\Main\Event $event) {
        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, [
            'foo_func' => [\Namespace\Some\Class::class, 'fooFunc'],
            'bar_func' => [\Namespace\Some\Second\Class::class, 'barFunc'],
        ]);
    })
    ```
    Или даже проще:
    ```php
    \Itgro\Twig\Extension\Functions::setHandlers([
        'foo_func' => [\Namespace\Some\Class::class, 'fooFunc'],
    ]);
    ```

* Возможность добавить свои фильтры для Twig'а посредством навешивания обработчиков на `\Itgro\Twig\Extension\Filters::EXPAND_HANDLERS_EVENT`. Код будет выглядеть примерно как в коде выше.

* Возможность создавать динамические агенты через классы. Каждый такой класс должен быть дочерним от класса `\Itgro\Cron\Agent` и иметь метод `call()`. Если Ваш агент должен принимать параметры и возвращать их же - делаете это в этом же методе.<br>
Само название функции либо можно прописать явно в параметре `name` агента-класса, либо само имя класса приведётся к camel_case'у, из-за чего имя функции преобразуется вида `\Itgro\Cron\Agent -> itgro_cron_agent`.

* Возможность добавить своих агентов-классы через стандартную инциализацию:
    ```php
    event_manager()->addEventHandler('extensions', \Itgro\Cron\Kernel::EXPAND_HANDLERS_EVENT, function () {
        return new EventResult(EventResult::SUCCESS, [
            \Namespace\Some\FirstAgent::class,
            \Namespace\Some\SecondAgent::class,
        ]);
    });
    ```
    Или даже проще:
    ```php
    \Itgro\Cron\Kernel::setHandlers([
        \Namespace\Some\Agent::class,
    ]);
    ```

#### Манипуляции с административной панелью:

* Манипуляция с определённым свойством на странице редактирования элемента:
    ```php
    (new \Namespace\IBlockExtended\Entity)->addElementPropertyInformation(
        'IBLOCK_PROPERTY_CODE', // Код свойства, для которого отрабатывается js-функция
        function ($propertyId) {
            // JavaScript-код или JavaScript-функция, которые будут обрабатываться только на странице, где есть свойство с указанным кодом
            return 'javascriptFunctionName';
        }
    )
    ```

* Добавление кнопки на страницу связанных сущностей с фильтрацией по текущей сущности на страницу редактирования элемента
    ```php
    (new \Namespace\IBlockExtended\Entity)->addElementAdminButton(
        (new \Itgro\Bitrix\Admin\Button\ElementEdit)
            ->withProperties([
                'iblock_type' => 'iblock_type_id', // Тип ИБ связанной страницы
                'iblock_code' => 'iblock_code', // Код ИБ связанной страницы
                'filter' => [
                    // Фильтр по текущей сущности
                    ['type' => 'property', 'code' => 'RELATED_PROPERTY_CODE'],
                ],
            ])
            ->withView([
                'place' => 'before', // Добавлять до или после кнопки копирования
                'style' => 'adm-btn-green', // Доп.классы для кнопки
                'name' => 'Элементы', // Заголовок кнопки
            ])
    );
    ```
    
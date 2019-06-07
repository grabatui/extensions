<?php

namespace Itgro\Entity\IBlock;

use BadMethodCallException;
use Bitrix\Catalog\PriceTable;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\SectionTable;

/**
 * Позволяет добавлять обработчики событий для элементов только под конкретный ИБ
 *
 * @method mixed beforeAdd($callback, $sort = 100)
 * @method mixed afterAdd($callback, $sort = 100)
 * @method mixed beforeUpdate($callback, $sort = 100)
 * @method mixed afterUpdate($callback, $sort = 100)
 * @method mixed beforeDelete($callback, $sort = 100)
 * @method mixed afterDelete($callback, $sort = 100)
 *
 * @method mixed afterPriceAdd($callback, $sort = 100)
 * @method mixed afterPriceUpdate($callback, $sort = 100)
 *
 * @method mixed beforeSectionAdd($callback, $sort = 100)
 * @method mixed afterSectionAdd($callback, $sort = 100)
 * @method mixed beforeSectionUpdate($callback, $sort = 100)
 * @method mixed afterSectionUpdate($callback, $sort = 100)
 * @method mixed beforeSectionDelete($callback, $sort = 100)
 */
trait WithEvents
{
    private $eventHandlers = [
        // Element
        'beforeAdd' => ['OnBeforeIBlockElementAdd', 'checkWithParams'],
        'afterAdd' => ['OnAfterIBlockElementAdd', 'checkWithParams'],
        'beforeUpdate' => ['OnBeforeIBlockElementUpdate', 'checkWithParams'],
        'afterUpdate' => ['OnAfterIBlockElementUpdate', 'checkWithParams'],
        'beforeDelete' => ['OnBeforeIBlockElementDelete', 'checkWithId'],
        'afterDelete' => ['OnAfterIBlockElementDelete', 'checkWithParams'],
        // Price
        'afterPriceAdd' => ['OnPriceAdd', 'checkWithPriceId', 'catalog'],
        'afterPriceUpdate' => ['OnPriceUpdate', 'checkWithPriceId', 'catalog'],
        // Section
        'beforeSectionAdd' => ['OnBeforeIBlockSectionAdd', 'checkWithSectionParams'],
        'afterSectionAdd' => ['OnAfterIBlockSectionAdd', 'checkWithSectionParams'],
        'beforeSectionUpdate' => ['OnBeforeIBlockSectionUpdate', 'checkWithSectionParams'],
        'afterSectionUpdate' => ['OnAfterIBlockSectionUpdate', 'checkWithSectionParams'],
        'beforeSectionDelete' => ['OnBeforeIBlockSectionDelete', 'checkWithSectionId'],
    ];

    public function __call($name, $arguments)
    {
        $parentCall = parent::__call($name, $arguments);

        if ($parentCall) {
            return $parentCall;
        }

        if (!array_key_exists($name, $this->eventHandlers) || count($arguments) < 1) {
            return null;
        }

        $callback = $arguments[0];

        $sort = array_get($arguments, 1, 100);

        list($eventName, $checkCallback, $module) = array_get($this->eventHandlers, $name);

        $this->addEvent($eventName, $checkCallback, $callback, $sort, $module);

        return $this;
    }

    public function checkWithParams($params, $isSection = false)
    {
        if (array_get($params, 'IBLOCK_ID')) {
            return (array_get($params, 'IBLOCK_ID') == $this->getIBlockId());
        }

        $id = array_get($params, 'ID');

        if ($id) {
            return ($isSection) ? $this->checkWithSectionId($id) : $this->checkWithId($id);
        }

        return false;
    }

    public function checkWithSectionParams($params)
    {
        return $this->checkWithParams($params, true);
    }

    public function checkWithId($id)
    {
        /** @var Entity $this */
        $item = ElementTable::getRow([
            'filter' => [
                '=ID' => $id,
                '=IBLOCK.CODE' => $this->getIBlockCode(),
            ],
            'select' => ['ID'],
        ]);

        return (!empty($item));
    }

    public function checkWithPriceId($id, $params = [])
    {
        $price = PriceTable::getRow([
            'filter' => [
                '=ID' => $id,
                '=ELEMENT.IBLOCK.CODE' => $this->getIBlockCode(),
            ],
            'select' => ['ID'],
        ]);

        return (!empty($price));
    }

    public function checkWithSectionId($id)
    {
        /** @var Entity $this */
        $section = SectionTable::getRow([
            'filter' => [
                '=ID' => $id,
                '=IBLOCK.CODE' => $this->getIBlockCode(),
            ],
            'select' => ['ID'],
        ]);

        return (!empty($section));
    }

    protected function addEvent($name, $checkCallback, $callback, $sort = 100, $moduleId = 'iblock')
    {
        event_manager()->addEventHandlerCompatible(
            $moduleId,
            $name,
            function () use ($checkCallback, $callback) {
                if (!call_user_func_array([$this, $checkCallback], func_get_args())) {
                    return null;
                }

                return call_user_func_array($callback, func_get_args());
            },
            false,
            $sort
        );
    }
}

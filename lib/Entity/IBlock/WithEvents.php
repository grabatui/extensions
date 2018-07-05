<?php

namespace Itgro\Entity\IBlock;

use Bitrix\Catalog\PriceTable;
use Bitrix\Iblock\ElementTable;

/**
 * Позволяет добавлять обработчики событий для элементов только под конкретный ИБ
 */
trait WithEvents
{
    public function beforeAdd($callback, $sort = 100)
    {
        $this->addEvent('OnBeforeIBlockElementAdd', 'checkWithParams', $callback, $sort);
    }

    public function afterAdd($callback, $sort = 100)
    {
        $this->addEvent('OnAfterIBlockElementAdd', 'checkWithParams', $callback, $sort);
    }

    public function beforeUpdate($callback, $sort = 100)
    {
        $this->addEvent('OnBeforeIBlockElementUpdate', 'checkWithParams', $callback, $sort);
    }

    public function afterUpdate($callback, $sort = 100)
    {
        $this->addEvent('OnAfterIBlockElementUpdate', 'checkWithParams', $callback, $sort);
    }

    public function beforeDelete($callback, $sort = 100)
    {
        $this->addEvent('OnBeforeIBlockElementDelete', 'checkWithId', $callback, $sort);
    }

    public function afterDelete($callback, $sort = 100)
    {
        $this->addEvent('OnAfterIBlockElementDelete', 'checkWithParams', $callback, $sort);
    }

    public function afterPriceAdd($callback, $sort = 100)
    {
        $this->addEvent('OnPriceAdd', 'checkWithPriceId', $callback, $sort, 'catalog');
    }

    public function afterPriceUpdate($callback, $sort = 100)
    {
        $this->addEvent('OnPriceUpdate', 'checkWithPriceId', $callback, $sort, 'catalog');
    }

    public function checkWithParams($params)
    {
        if (array_get($params, 'IBLOCK_ID')) {
            return (array_get($params, 'IBLOCK_ID') == get_iblock_id($this->getIBlockCode()));
        }

        if (array_get($params, 'ID')) {
            return $this->checkWithId(array_get($params, 'ID'));
        }

        return false;
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

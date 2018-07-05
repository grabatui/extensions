<?php

namespace Itgro\Bitrix\Sale;

use Bitrix\Sale\Internals\OrderPropsTable;
use Itgro\WithCachedEntities;

class OrderProperty
{
    use WithCachedEntities;

    public static function getByCode($code)
    {
        check_modules('sale');

        if (self::cacheHas($code)) {
            return self::cacheGet($code);
        }

        $property = OrderPropsTable::getRow([
            'filter' => ['=CODE' => $code],
            'select' => ['ID'],
        ]);

        self::cacheSet($code, (!empty($property)) ? $property['ID'] : 0);

        return self::cacheGet($code);
    }
}

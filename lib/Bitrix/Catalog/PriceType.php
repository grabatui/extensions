<?php

namespace Itgro\Bitrix\Catalog;

use Bitrix\Catalog\GroupTable;
use Itgro\WithCachedEntities;

class PriceType
{
    use WithCachedEntities;

    public static function getByCode($code)
    {
        check_modules('catalog');

        if (self::cacheHas($code)) {
            return self::cacheGet($code);
        }

        $type = GroupTable::getRow([
            'filter' => ['=NAME' => $code],
            'select' => ['ID'],
        ]);

        self::cacheSet($code, (!empty($type)) ? $type['ID'] : 0);

        return self::cacheGet($code);
    }
}

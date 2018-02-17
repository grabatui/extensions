<?php

namespace Itgro\Bitrix;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;
use Itgro\WithCachedEntities;

class Iblock
{
    use WithCachedEntities;

    public static function getByCode($code)
    {
        if (!Loader::includeModule('iblock')) {
            return 0;
        }

        if (self::cacheHas($code)) {
            return self::cacheGet($code);
        }

        $iBlock = IblockTable::getRow([
            'filter' => ['=CODE' => $code],
            'select' => ['ID'],
        ]);

        self::cacheSet($code, (!empty($iBlock)) ? $iBlock['ID'] : 0);

        return self::cacheGet($code);
    }
}

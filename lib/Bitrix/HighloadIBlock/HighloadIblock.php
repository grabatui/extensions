<?php

namespace Itgro\Bitrix\HighloadIBlock;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Itgro\WithCachedEntities;

class HighloadIblock
{
    use WithCachedEntities;

    public static function getByName($name)
    {
        check_modules('highloadblock');

        if (self::cacheHas($name)) {
            return self::cacheGet($name);
        }

        $highloadIBlock = HighloadBlockTable::getRow([
            'filter' => ['=NAME' => $name],
            'select' => ['ID'],
        ]);

        self::cacheSet($name, (!empty($highloadIBlock)) ? $highloadIBlock['ID'] : 0);

        return self::cacheGet($name);
    }
}

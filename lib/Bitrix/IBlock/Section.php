<?php

namespace Itgro\Bitrix\IBlock;

use Bitrix\Iblock\SectionTable;
use Itgro\WithCachedEntities;

class Section
{
    use WithCachedEntities;

    public static function getByCode($code, $iBlock = null)
    {
        check_modules('iblock');

        if ($iBlock && !is_numeric($iBlock) && strlen($iBlock) > 0) {
            $iBlock = get_iblock_id($iBlock);
        }

        $cacheKey = self::cacheCreateKey($iBlock, $code);

        if (self::cacheHas($cacheKey)) {
            return self::cacheGet($cacheKey);
        }

        $filter = ['=CODE' => $code];

        if ($iBlock) {
            $filter['=IBLOCK_ID'] = $iBlock;
        }

        $section = SectionTable::getRow([
            'filter' => $filter,
            'select' => ['ID'],
        ]);

        self::cacheSet($cacheKey, (!empty($section)) ? $section['ID'] : 0);

        return self::cacheGet($cacheKey);
    }
}

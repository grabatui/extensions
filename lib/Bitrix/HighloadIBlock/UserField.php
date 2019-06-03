<?php

namespace Itgro\Bitrix\HighloadIBlock;

use Bitrix\Main\UserFieldTable;
use Itgro\WithCachedEntities;

class UserField
{
    use WithCachedEntities;

    public static function getByCode($code, $highloadBlock = null)
    {
        if ($highloadBlock && !is_numeric($highloadBlock) && strlen($highloadBlock) > 0) {
            $highloadBlock = get_highload_iblock_id($highloadBlock);
        }

        $cacheKey = self::cacheCreateKey($highloadBlock, $code);

        if (self::cacheHas($cacheKey)) {
            return self::cacheGet($cacheKey);
        }

        $filter = ['=FIELD_NAME' => $code];

        if ($highloadBlock) {
            $filter['=ENTITY_ID'] = sprintf('HLBLOCK_%d', $highloadBlock);
        }

        $userField = UserFieldTable::getRow([
            'filter' => $filter,
            'select' => ['ID'],
        ]);

        self::cacheSet($cacheKey, (!empty($userField)) ? $userField['ID'] : 0);

        return self::cacheGet($cacheKey);
    }
}

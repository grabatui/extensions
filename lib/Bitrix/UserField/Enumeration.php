<?php

namespace Itgro\Bitrix\UserField;

use CUserFieldEnum;
use Itgro\WithCachedEntities;

class Enumeration
{
    use WithCachedEntities;

    public static function getByValue($value, $userField, $highloadBlock)
    {
        check_modules('iblock');

        if ($highloadBlock && !is_numeric($highloadBlock) && strlen($highloadBlock) > 0) {
            $highloadBlock = get_highload_iblock_id($highloadBlock);
        }

        if ($userField && !is_numeric($userField) && strlen($userField) > 0) {
            $userField = get_user_field_id($userField, $highloadBlock);
        }

        $cacheKey = self::cacheCreateKey($highloadBlock, $userField, $value);

        if (self::cacheHas($cacheKey)) {
            return self::cacheGet($cacheKey);
        }

        $filter = ['VALUE' => $value];

        if ($userField) {
            $filter['USER_FIELD_ID'] = $userField;
        }

        $userFieldEnum = (new CUserFieldEnum)->GetList([], $filter)->Fetch();

        self::cacheSet($cacheKey, (!empty($userFieldEnum)) ? $userFieldEnum['ID'] : 0);

        return self::cacheGet($cacheKey);
    }
}

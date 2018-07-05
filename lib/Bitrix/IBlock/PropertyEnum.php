<?php

namespace Itgro\Bitrix\IBlock;

use Bitrix\Iblock\PropertyEnumerationTable;
use Itgro\WithCachedEntities;

class PropertyEnum
{
    use WithCachedEntities;

    public static function getByXmlId($xmlId, $property = null, $iBlock = null)
    {
        check_modules('iblock');

        if ($iBlock && !is_numeric($iBlock) && strlen($iBlock) > 0) {
            $iBlock = get_iblock_id($iBlock);
        }

        if ($property && !is_numeric($property) && strlen($property) > 0) {
            $property = get_property_id($property, $iBlock);
        }

        $cacheKey = self::cacheCreateKey($iBlock, $property, $xmlId);

        if (self::cacheHas($cacheKey)) {
            return self::cacheGet($cacheKey);
        }

        $filter = ['=XML_ID' => $xmlId];

        if ($iBlock) {
            $filter['=PROPERTY.IBLOCK_ID'] = $iBlock;
        }

        if ($property) {
            $filter['=PROPERTY_ID'] = $property;
        }

        $propertyEnum = PropertyEnumerationTable::getRow([
            'filter' => $filter,
            'select' => ['ID'],
        ]);

        self::cacheSet($cacheKey, (!empty($propertyEnum)) ? $propertyEnum['ID'] : 0);

        return self::cacheGet($cacheKey);
    }
}

<?php

namespace Itgro\Bitrix\IBlock;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Loader;
use Itgro\WithCachedEntities;

class Property
{
    use WithCachedEntities;

    private $fields = [
        'NAME' => null,
        'CODE' => null,
        'IBLOCK_ID' => null,
        'SORT' => 500,
        'PROPERTY_TYPE' => 'S',
        'MULTIPLE'  => 'N',
        'IS_REQUIRED' => 'N',
    ];

    public function __construct($fields = [])
    {
        $this->expand($fields);
    }

    public function set($field, $value)
    {
        $this->fields[$field] = $value;

        return $this;
    }

    public function expand($fields)
    {
        $this->fields = array_merge($this->fields, $fields);

        return $this;
    }

    public function multiple()
    {
        return $this->set('MULTIPLE', 'Y');
    }

    public function required()
    {
        return $this->set('IS_REQUIRED', 'Y');
    }

    public function withType($type, $additional = null)
    {
        $this->set('PROPERTY_TYPE', $type);

        return (is_array($additional)) ? $this->expand($additional) : $this;
    }

    public function toArray()
    {
        return $this->fields;
    }

    public static function create($code, $name, $iBlockId)
    {
        return new self([
            'CODE' => $code,
            'NAME' => $name,
            'IBLOCK_ID' => $iBlockId,
        ]);
    }

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

        $property = PropertyTable::getRow([
            'filter' => $filter,
            'select' => ['ID'],
        ]);

        self::cacheSet($cacheKey, (!empty($property)) ? $property['ID'] : 0);

        return self::cacheGet($cacheKey);
    }
}

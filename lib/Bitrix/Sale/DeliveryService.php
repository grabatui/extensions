<?php

namespace Itgro\Bitrix\Sale;

use Bitrix\Sale\Delivery\Services\Table;
use Itgro\WithCachedEntities;

class DeliveryService
{
    use WithCachedEntities;

    public static function getByCode($code)
    {
        check_modules('sale');

        if (self::cacheHas($code)) {
            return self::cacheGet($code);
        }

        $delivery = Table::getRow([
            'filter' => [
                '=ACTIVE' => 'Y',
                '=CODE' => $code,
            ],
            'select' => ['ID'],
        ]);

        self::cacheSet($code, (!empty($delivery)) ? $delivery['ID'] : 0);

        return self::cacheGet($code);
    }
}

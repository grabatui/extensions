<?php

namespace Itgro\Bitrix\Sale;

use Bitrix\Sale\Internals\PaySystemActionTable;
use Itgro\WithCachedEntities;

class PaySystem
{
    use WithCachedEntities;

    public static function getByCode($code)
    {
        check_modules('sale');

        if (self::cacheHas($code)) {
            return self::cacheGet($code);
        }

        $paySystem = PaySystemActionTable::getRow([
            'filter' => [
                '=ACTIVE' => 'Y',
                '=CODE' => $code,
            ],
            'select' => ['PAY_SYSTEM_ID'],
        ]);

        self::cacheSet($code, (!empty($paySystem)) ? $paySystem['PAY_SYSTEM_ID'] : 0);

        return self::cacheGet($code);
    }
}

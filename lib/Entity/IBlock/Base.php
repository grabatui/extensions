<?php

namespace Itgro\Entity\IBlock;

use Bitrix\Main\Loader;
use CDBResult;
use CIBlockElement;
use Exception;
use Itgro\Bitrix\Admin\WithAdditionalExtensions;
use Itgro\Entity\Base as BaseEntity;

abstract class Base extends BaseEntity
{
    use WithEvents {
        __call as public callMethod;
    }

    use WithRandomShow;
    use WithAdditionalExtensions;

    protected $iBlockCode;

    public function __construct()
    {
        if (!Loader::includeModule('iblock')) {
            throw new Exception('Ошибка во время подключения модуля информационных блоков');
        }
    }

    public function getIBlockId()
    {
        return get_iblock_id($this->getIBlockCode());
    }

    public function getIBlockCode()
    {
        return $this->iBlockCode;
    }

    protected function getObjects(): CDBResult
    {
        $this->expandFilter(['IBLOCK_ID' => $this->getIBlockId()]);

        $CIBlockElement = new CIBlockElement();
        return $CIBlockElement->GetList(
            $this->getOrder(),
            $this->getFilter(),
            $this->getGroupBy(),
            $this->getNavParams(),
            $this->getSelect()
        );
    }

    protected function expandOneItemParameters()
    {
        $this->expandNavParams(['nTopCount' => 1]);
    }

    public function getCount(): int
    {
        $this->withGroupBy([]);

        $this->expandFilter(['IBLOCK_ID' => $this->getIBlockId()]);

        $CIBlockElement = new CIBlockElement();
        return $CIBlockElement->GetList($this->order, $this->filter, $this->groupBy);
    }

    public function add($fields): int
    {
        $fields = array_merge(
            [
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => $this->getIBlockId(),
            ],
            $fields
        );

        $CIBlockElement = new CIBlockElement();

        $result = $CIBlockElement->Add($fields);

        if (!$result) {
            throw new Exception($CIBlockElement->LAST_ERROR);
        }

        return $result;
    }
}

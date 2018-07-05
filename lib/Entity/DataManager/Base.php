<?php

namespace Itgro\Entity\DataManager;

use Bitrix\Main\Entity\DataManager as BaseDataManager;
use CDBResult;
use Itgro\Entity\Base as BaseEntity;

abstract class Base extends BaseEntity
{
    /** @var BaseDataManager */
    protected $dataManager;

    public function __construct()
    {
        if (!is_object($this->dataManager)) {
            $this->dataManager = new $this->dataManager;
        }
    }

    protected function getObjects(): CDBResult
    {
        $rsItems = $this->dataManager->getList($this->createParameters());

        $CDBResult = new CDBResult();
        $CDBResult->InitFromArray($rsItems->fetchAll());

        return $CDBResult;
    }

    protected function expandOneItemParameters()
    {
        $this->expandNavParams(['limit' => 1]);
    }

    private function createParameters(): array
    {
        $parameters = array_filter([
            'order' => $this->getOrder(),
            'filter' => $this->getFilter(),
            'select' => $this->getSelect(),
            'group' => $this->getGroupBy(),
        ]);

        return array_merge(
            $parameters,
            // В $this->navParams доступны лишь перечисленные ниже ключи. Остальные - не используются
            (!empty($this->getNavParams())) ?
                array_intersect_key(
                    $this->getNavParams(),
                    array_flip(['limit', 'offset', 'count_total', 'runtime', 'data_doubling', 'cache'])
                ) :
                []
        );
    }
}

<?php

namespace Itgro\Entity\HighloadIBlock;

use Itgro\CanCreatedAsEntity;
use Itgro\Entity\DataManager\Base as DataManagerBase;

abstract class Base extends DataManagerBase
{
    use CanCreatedAsEntity;

    protected $entityName = '';

    public function __construct()
    {
        $this->dataManager = get_highload_iblock_entity($this->entityName);

        parent::__construct();
    }

    /**
     * @param bool $withProcessing
     * @param string $byColumn
     * @return self[]
     */
    public function getMany($withProcessing = true, $byColumn = 'ID')
    {
        $items = parent::getMany($withProcessing, $byColumn);

        $formatted = [];
        foreach ($items as $key => $item) {
            $formatted[$key] = self::create(array_get($item, 'ID'), $item);
        }

        return $formatted;
    }
}

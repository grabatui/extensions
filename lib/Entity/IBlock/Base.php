<?php

namespace Itgro\Entity\IBlock;

use Bitrix\Main\Loader;
use CDBResult;
use CIBlockElement;
use Exception;
use Itgro\Entity\Base as BaseEntity;

abstract class Base extends BaseEntity
{
    private $alreadyTriedGetRandom = false;

    public function __construct()
    {
        if (!Loader::includeModule('iblock')) {
            throw new Exception('Ошибка во время подключения модуля информационных блоков');
        }
    }

    protected function getObjects(): CDBResult
    {
        $this->expandFilter(['IBLOCK_ID' => get_iblock_id($this->iBlockCode)]);

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

        $this->expandFilter(['IBLOCK_ID' => get_iblock_id($this->iBlockCode)]);

        $CIBlockElement = new CIBlockElement();
        return $CIBlockElement->GetList($this->order, $this->filter, $this->groupBy);
    }

    public function getOneSectionRandom(int $sectionId, bool $needToCheckSession = true)
    {
        $this->withOrder(['RAND' => 'ASC']);

        $this->expandFilter([
            'ACTIVE' => 'Y',
            'IBLOCK_SECTION_ID' => $sectionId,
        ]);

        $alreadyShown = $this->getAlreadyShown($sectionId);

        if ($needToCheckSession) {
            $this->expandFilter(['!ID' => $alreadyShown]);
        }

        $element = $this->getOne();

        if (!$element && $needToCheckSession && !empty($alreadyShown) && !$this->alreadyTriedGetRandom) {
            $this->alreadyTriedGetRandom = true;

            // Чтобы не получилось так, что только что показанный рандомно выбирается первым же в новой итерации
            $lastAdded = array_pop($alreadyShown);

            $this->clearAlreadyShown($sectionId);

            if ($lastAdded && !empty($alreadyShown)) {
                $this->setAlreadyShown($sectionId, $lastAdded);
            }

            return $this->getOneSectionRandom($sectionId, $needToCheckSession);
        }

        $this->alreadyTriedGetRandom = false;

        $this->setAlreadyShown($sectionId, array_get($element, 'ID', 0));

        return $element;
    }

    public function add($fields): int
    {
        $fields = array_merge(
            [
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => get_iblock_id($this->iBlockCode),
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

    protected function clearAlreadyShown($sectionId = null)
    {
        if ($sectionId) {
            unset($_SESSION[$this->iBlockCode][$sectionId]);
        } else {
            unset($_SESSION[$this->iBlockCode]);
        }
    }

    protected function getAlreadyShown(int $sectionId)
    {
        return array_get(array_get($_SESSION, $this->iBlockCode, []), $sectionId, []);
    }

    protected function setAlreadyShown(int $sectionId, int $id)
    {
        if (!array_get($_SESSION, $this->iBlockCode)) {
            $_SESSION[$this->iBlockCode] = [];
        }

        if (!array_get($_SESSION[$this->iBlockCode], $sectionId)) {
            $_SESSION[$this->iBlockCode][$sectionId] = [];
        }

        $_SESSION[$this->iBlockCode][$sectionId][] = $id;

        $_SESSION[$this->iBlockCode][$sectionId] = array_unique(array_filter($_SESSION[$this->iBlockCode][$sectionId]));
    }
}

<?php

namespace Itgro\Entity\IBlock;

trait WithRandomShow
{
    private $alreadyTriedGetRandom = false;

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

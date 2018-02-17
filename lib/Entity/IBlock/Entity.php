<?php

namespace Itgro\Entity\IBlock;

use CIBlockElement;

/**
 * Представляет собой обёртку обычного выборщика из ИБ, но с оборачиванием в сам класс сущности
 * Т.о. позволяет вызывать различные методы для каждого из элементов выборки ИБ
 *
 * P.S. Если Вы вдруг захотели использовать эту логику по-другому, отключите у класса-сущности @see \Itgro\Entity\Base::wrapElements
 */
abstract class Entity extends Base
{
    private $id;
    private $fields;

    private $related = [];

    public static function create($id, $fields = []): self
    {
        $entity = new static();

        $entity->setId($id);
        $entity->setFields($fields);

        return $entity;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    public function getField($key)
    {
        return (!empty($this->fields)) ? array_get($this->fields, $key) : null;
    }

    public function setField($key, $value = null)
    {
        $this->fields[$key] = $value;
    }

    public function getPropertyEnum($key)
    {
        return $this->getField(sprintf('PROPERTY_%s_ENUM', $key));
    }

    public function getPropertyXmlId($key)
    {
        return $this->getField(sprintf('PROPERTY_%s_XML_ID', $key));
    }

    public function getPropertyValue($key)
    {
        return $this->getField(sprintf('PROPERTY_%s_VALUE', $key));
    }

    public function setProperties($propertyFilter = [])
    {
        $rsProperties = CIBlockElement::GetProperty(
            get_iblock_id($this->getIBlockCode()),
            $this->id,
            'SORT',
            'ASC',
            $propertyFilter
        );

        while ($property = $rsProperties->Fetch()) {
            (array_get($property, 'MULTIPLE', 'N') == 'Y') ?
                $this->setMultipleProperty($property) :
                $this->setProperty($property);
        }
    }

    public function setRelated($field, $entity)
    {
        $this->related[$field] = $entity;
    }

    public function getRelated($field)
    {
        return array_get($this->related, $field);
    }

    protected function setRelatedProperty(array $items, string $column, $callback)
    {
        $result = [];
        /** @var Entity $item */
        foreach ($items as $item) {
            $value = $item->getPropertyValue($column);

            if ($value) {
                $result[$value] = null;
            }
        }

        if (!empty($result) && is_callable($callback)) {
            $result = call_user_func($callback, $result);

            /** @var Entity $item */
            foreach ($items as $item) {
                $value = $item->getPropertyValue($column);

                if ($value) {
                    $item->setRelated($column, array_get($result, $value));
                }
            }
        }

        return $items;
    }

    private function setProperty(array $property)
    {
        $code = array_get($property, 'CODE');

        switch (array_get($property, 'PROPERTY_TYPE')) {
            case 'L':
                $this->setField(sprintf('PROPERTY_%s_ENUM', $code), array_get($property, 'VALUE_ENUM'));
                $this->setField(sprintf('PROPERTY_%s_XML_ID', $code), array_get($property, 'VALUE_XML_ID'));
                break;
        }

        $this->setField(sprintf('PROPERTY_%s_VALUE', $code), array_get($property, 'VALUE'));
    }

    private function setMultipleProperty(array $property)
    {
        $code = array_get($property, 'CODE');

        switch (array_get($property, 'PROPERTY_TYPE')) {
            case 'L':
                $this->setField(
                    sprintf('PROPERTY_%s_ENUM', $code),
                    expand_variable($this->getPropertyEnum($code), array_get($property, 'VALUE_ENUM'))
                );
                $this->setField(
                    sprintf('PROPERTY_%s_XML_ID', $code),
                    expand_variable($this->getPropertyXmlId($code), array_get($property, 'VALUE_XML_ID'))
                );
                break;
        }

        $this->setField(
            sprintf('PROPERTY_%s_VALUE', $code),
            expand_variable($this->getPropertyValue($code), array_get($property, 'VALUE'))
        );
    }
}

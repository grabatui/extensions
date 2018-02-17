<?php

namespace Itgro\Entity;

use CDBResult;
use Itgro\Entity\IBlock\Entity;

/**
 * @see Base::getQueryProperty()
 * @method array getOrder()
 * @method array getFilter()
 * @method array getSelect()
 * @method mixed getGroupBy()
 * @method mixed getNavParams()
 *
 * @see Base::withQueryProperty()
 * @method $this withOrder($value)
 * @method $this withFilter($value)
 * @method $this withSelect($value)
 * @method $this withGroupBy($value)
 * @method $this withNavParams($value)
 *
 * @see Base::expandQueryProperty()
 * @method $this expandOrder($expansion)
 * @method $this expandFilter($expansion)
 * @method $this expandSelect($expansion)
 * @method $this expandGroupBy($expansion)
 * @method $this expandNavParams($expansion)
 */
abstract class Base
{
    protected $iBlockCode;
    protected $defaultSelect = null;

    protected $order = [];
    protected $filter = [];
    protected $select = [];
    protected $groupBy = false;
    protected $navParams = false;

    protected $wrapElements = true;

    public function __call($name, $arguments)
    {
        $aliases = [
            'get' => 'getQueryProperty',
            'expand' => 'expandQueryProperty',
            'with' => 'withQueryProperty',
        ];

        foreach ($aliases as $search => $method) {
            if (strpos($name, $search) === 0) {
                $name = str_ireplace($search, '', $name);

                $name = mb_strtolower(mb_substr($name, 0, 1)) . mb_substr($name, 1);

                if (!array_key_exists($name, get_class_vars(__CLASS__))) {
                    break;
                }

                array_unshift($arguments, $name);

                return call_user_func_array([$this, $method], $arguments);
            }
        }

        return null;
    }

    public function getQueryProperty($field)
    {
        return $this->{$field};
    }

    public function expandQueryProperty($field, $expansion)
    {
        $this->withQueryProperty(
            $field,
            expand_variable(
                $this->getQueryProperty($field),
                $expansion
            )
        );

        return $this;
    }

    public function withQueryProperty($field, $value)
    {
        switch ($field) {
            case 'groupBy':
                $value = (is_array($value)) ? $value : false;
                break;

            case 'navParams':
                $value = (!empty($value) && is_array($value)) ? $value : false;
                break;

            default:
                $value = (!empty($value) && is_array($value)) ? $value : [];
                break;
        }

        $this->{$field} = $value;

        return $this;
    }

    public function getIBlockCode()
    {
        return $this->iBlockCode;
    }

    public function getMany($withProcessing = true, $byColumn = 'ID')
    {
        if (!$this->select && $this->defaultSelect) {
            $this->select = $this->defaultSelect;
        }

        $objects = $this->getObjects();

        $method = ($withProcessing) ? 'GetNext' : 'Fetch';

        $result = [];
        while ($item = $objects->{$method}()) {
            $entity = ($this->wrapElements && $this instanceof Entity) ?
                $this::create(array_get($item, 'ID'), $item) :
                $item;

            ($byColumn && array_key_exists($byColumn, $item)) ?
                $result[$item[$byColumn]] = $entity :
                $result[] = $entity;
        }

        return $result;
    }

    public function getOne($withProcessing = true)
    {
        $this->expandNavParams(['nTopCount' => 1]);

        $items = $this->getMany($withProcessing);

        return (!empty($items)) ? reset($items) : null;
    }

    abstract protected function getObjects(): CDBResult;
}

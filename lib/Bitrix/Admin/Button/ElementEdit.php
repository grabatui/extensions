<?php

namespace Itgro\Bitrix\Admin\Button;

class ElementEdit extends Base
{
    protected function getLinkReplaces()
    {
        $query = [
            'type' => array_get($this->properties, 'iblock_type'),
            'IBLOCK_ID' => get_iblock_id(array_get($this->properties, 'iblock_code')),
            'set_filter' => 'Y',
        ];

        if (!empty(array_get($this->properties, 'filter'))) {
            $this->setFilters($query);
        }

        return ['/bitrix/admin/iblock_element_admin.php?%s', http_build_query($query)];
    }

    protected function setFilters(&$query)
    {
        foreach (array_get($this->properties, 'filter') as $filter) {
            switch (array_get($filter, 'type')) {
                case 'property':
                    $property = sprintf(
                        'find_el_property_%d',
                        get_property_id(array_get($filter, 'code'), array_get($this->properties, 'iblock_code'))
                    );

                    $query[$property] = request()->getQuery('ID');
                    break;
            }
        }
    }
}

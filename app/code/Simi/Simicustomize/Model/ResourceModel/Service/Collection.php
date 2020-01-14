<?php

/**
 * Connector Resource Collection
 */

namespace Simi\Simicustomize\Model\ResourceModel\Service;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Simi\Simicustomize\Model\Service', 'Simi\Simicustomize\Model\ResourceModel\Service');
    }
}

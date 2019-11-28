<?php

namespace Simi\Simicustomize\Model\ResourceModel;

/**
 * Connector Resource Model
 */
class Newcollections extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('simiconnector_newcollections', 'newcollections_id');
    }
}

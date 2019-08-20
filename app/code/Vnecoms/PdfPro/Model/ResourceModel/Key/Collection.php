<?php

namespace Vnecoms\PdfPro\Model\ResourceModel\Key;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Vnecoms\PdfPro\Model\Key', 'Vnecoms\PdfPro\Model\ResourceModel\Key');
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            $item->setData('store_ids', explode(',', $item->getData('store_ids')));
        }
        return $this;
    }
}

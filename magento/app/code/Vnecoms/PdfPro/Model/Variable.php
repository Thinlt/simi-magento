<?php

namespace Vnecoms\PdfPro\Model;

use Magento\Framework\Model\AbstractModel;

class Variable extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Vnecoms\PdfPro\Model\ResourceModel\Variable');
    }
}

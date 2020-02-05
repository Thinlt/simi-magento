<?php

namespace Vnecoms\PdfPro\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Category.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Category extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Vnecoms\PdfPro\Model\ResourceModel\Category');
    }
}

<?php

namespace Vnecoms\PdfPro\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Category.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Category extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('ves_pdfpro_category', 'category_id');
    }
}

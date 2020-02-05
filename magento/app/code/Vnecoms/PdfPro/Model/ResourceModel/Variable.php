<?php

namespace Vnecoms\PdfPro\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Variable.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Variable extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('ves_pdfpro_variable', 'entity_id');
    }
}

<?php

namespace Vnecoms\PdfPro\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Template.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Template extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('ves_pdfpro_template', 'id');
    }
}

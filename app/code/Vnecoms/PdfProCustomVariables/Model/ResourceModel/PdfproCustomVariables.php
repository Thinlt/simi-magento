<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 23/01/2017
 * Time: 22:42
 */

namespace Vnecoms\PdfProCustomVariables\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PdfproCustomVariables extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('ves_pdfprocustomvariables_customvariables', 'custom_variable_id');
    }
}

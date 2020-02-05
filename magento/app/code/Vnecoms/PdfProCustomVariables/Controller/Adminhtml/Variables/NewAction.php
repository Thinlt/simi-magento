<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 23/01/2017
 * Time: 22:55
 */

namespace Vnecoms\PdfProCustomVariables\Controller\Adminhtml\Variables;

use Vnecoms\PdfProCustomVariables\Controller\Adminhtml\Variables;

class NewAction extends Variables
{
    /**
     * New action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}

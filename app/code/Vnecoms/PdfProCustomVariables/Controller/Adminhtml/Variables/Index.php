<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 23/01/2017
 * Time: 22:56
 */

namespace Vnecoms\PdfProCustomVariables\Controller\Adminhtml\Variables;

use Vnecoms\PdfProCustomVariables\Controller\Adminhtml\Variables;

class Index extends Variables
{
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Manage Variables'), __('Manage Variables'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Variables'));
        $this->_view->renderLayout('root');
    }
}

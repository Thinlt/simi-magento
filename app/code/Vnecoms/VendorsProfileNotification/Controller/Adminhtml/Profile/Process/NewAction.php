<?php
namespace Vnecoms\VendorsProfileNotification\Controller\Adminhtml\Profile\Process;

use Vnecoms\Vendors\Controller\Adminhtml\Action;

class NewAction extends Action
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}

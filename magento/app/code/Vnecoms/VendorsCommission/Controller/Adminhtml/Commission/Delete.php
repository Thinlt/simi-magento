<?php
namespace Vnecoms\VendorsCommission\Controller\Adminhtml\Commission;

use Vnecoms\Vendors\Controller\Adminhtml\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Action
{
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_VendorsCommission::commission_configuration');
    }
    
    /**
     * @return void
     */
    public function execute()
    {
        try {
            /** @var \Magento\CatalogRule\Model\Rule $model */
            $model = $this->_objectManager->create('Vnecoms\VendorsCommission\Model\Rule');

            $id = $this->getRequest()->getParam('id');
            $model->setId($id)->delete();
            
            $this->messageManager->addSuccess(__('You deleted the commission rule.'));
            
            return $this->_redirect('vendors/commission/');
        } catch (\Exception $e) {
            $this->messageManager->addError(
                $e->getMessage()
            );
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->_redirect('vendors/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
    }
}

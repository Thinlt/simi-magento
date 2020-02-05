<?php
namespace Vnecoms\Vendors\Controller\Adminhtml\Index;

use Vnecoms\Vendors\Controller\Adminhtml\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

class Save extends Action
{
    /**
     * @var \Vnecoms\Vendors\Model\VendorFactory
     */
    protected $vendorFactory;
    
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_Vendors::vendors_sellers');
    }
    
    
    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Date $dateFilter,
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory
    ) {
        $this->vendorFactory = $vendorFactory;
        
        parent::__construct($context, $coreRegistry, $dateFilter);
    }
    
    
    /**
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                // optional fields might be set in request for future processing by observers in other modules
                $vendorData = $this->getRequest()->getParam('vendor_data');
                $vendorId = $this->getRequest()->getParam('id');
                $request = $this->getRequest();
                $isExistingVendor = (bool)$vendorId;
                $vendor = $this->vendorFactory->create();
                if ($isExistingVendor) {
                    $vendor->load($vendorId);
                }

                $vendor->addData($vendorData);
                
                $this->_eventManager->dispatch(
                    'adminhtml_vendor_prepare_save',
                    ['vendor' => $vendor, 'request' => $request]
                );

                // Save vendor
                $vendor->save();


                // After save
                $this->_eventManager->dispatch(
                    'adminhtml_vendor_save_after',
                    ['vendor' => $vendor, 'request' => $request]
                );
                // Done Saving customer, finish save action
                $this->_coreRegistry->register('current_vendor_id', $vendorId);
                $this->messageManager->addSuccess(__('You saved the vendor.'));
                
                
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('vendors/*/edit', ['id' => $vendorId]);
                    return;
                }
                $this->_redirect('vendors/index/');
                
                return;
            } catch (\Magento\Eav\Model\Entity\Attribute\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('vendors/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the seller data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('vendors/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('vendors/*/');
    }
}

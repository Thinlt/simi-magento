<?php
namespace Vnecoms\Vendors\Controller\Vendors\Account;

use \Vnecoms\Vendors\Controller\Vendors\Action;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_Vendors::account';
    
    /**
     * @return void
     */
    public function execute()
    {
        $request = $this->getRequest();
        if ($request->getPostValue()) {
            try {
                $vendorData = $request->getParam('vendor_data');
                $vendor = $this->_session->getVendor();

                $notAllowedAttributes = $this->_helper->getNotSavedVendorAttributes();
                foreach ($notAllowedAttributes as $attr) {
                    unset($vendorData[$attr]);
                }
                
                $vendor->addData($vendorData);
                
                $this->_eventManager->dispatch(
                    'vendor_account_prepare_save',
                    ['vendor' => $vendor, 'request' => $request]
                );

                // Save vendor
                $vendor->save();


                // After save
                $this->_eventManager->dispatch(
                    'vendor_account_save_after',
                    ['vendor' => $vendor, 'request' => $request]
                );
                // Done Saving customer, finish save action
                $this->_coreRegistry->register('current_vendor_id', $vendor->getId());
                $this->messageManager->addSuccess(__('You account information is saved.'));
                
                $this->_redirect('account');
            } catch (\Magento\Eav\Model\Entity\Attribute\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the seller data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }
        $this->_redirect('account');
    }
}

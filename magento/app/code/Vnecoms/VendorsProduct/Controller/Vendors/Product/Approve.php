<?php

namespace Vnecoms\VendorsProduct\Controller\Vendors\Product;

use Magento\Framework\Controller\ResultFactory;
use Vnecoms\VendorsProduct\Model\Source\Approval as ProductApproval;

class Approve extends \Vnecoms\VendorsProduct\Controller\Vendors\Product
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_Vendors::product_action_save';
    
    /**
     * Update product(s) status action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $product = $this->productBuilder->build($this->getRequest());
            if (!$product->getId()) {
                $this->messageManager->addError(__('This product no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('catalog/product/');
            }
            $approvalStatus = in_array($product->getApproval(), [ProductApproval::STATUS_NOT_SUBMITED, ProductApproval::STATUS_UNAPPROVED])?
                                ProductApproval::STATUS_PENDING:
                                ProductApproval::STATUS_PENDING_UPDATE;
            
            $product->setApproval($approvalStatus)->getResource()->saveAttribute($product, 'approval');

            $vendorProductHelper =  \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Vnecoms\VendorsProduct\Helper\Data');

            if($approvalStatus == ProductApproval::STATUS_PENDING){
                /*Send new product approval notification email to admin*/
                $vendorProductHelper->sendNewProductApprovalEmailToAdmin($product, $this->_getSession()->getVendor());
            }else{
                $vendorProductHelper->sendUpdateProductApprovalEmailToAdmin($product, $this->_getSession()->getVendor());
            }


            $this->messageManager->addSuccess(__('Your product %1 has been submited for approval.', $product->getName()));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while updating the product(s) status.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('catalog/product/edit', ['id' => $this->getRequest()->getParam('id')]);
    }
}

<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Controller\Adminhtml\Catalog\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Vnecoms\VendorsProduct\Model\Source\Approval;

class Reject extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * @var \Vnecoms\VendorsProduct\Helper\Data
     */
    protected $productHelper;
    
    /**
     * @var \Vnecoms\Vendors\Model\VendorFactory
     */
    protected $vendorFactory;
    
    /**
     * @param Context $context
     * @param Builder $productBuilder
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        \Vnecoms\VendorsProduct\Helper\Data $productHelper,
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory
    ) {
        $this->productHelper = $productHelper;
        $this->vendorFactory = $vendorFactory;
        
        parent::__construct($context, $productBuilder);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product');
        $product->load($id);
        if (!$product->getId()) {
            $this->messageManager->addError(__('This product no longer exists.'));
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('vendors/catalog_product/');
        }
        
        $updateCollection = $this->_objectManager->create('Vnecoms\VendorsProduct\Model\ResourceModel\Product\Update\Collection');
        $updateCollection->addFieldToFilter('product_id', $product->getId())
            ->addFieldToFilter('status', \Vnecoms\VendorsProduct\Model\Product\Update::STATUS_PENDING);
        foreach ($updateCollection as $update) {
            $update->setStatus(\Vnecoms\VendorsProduct\Model\Product\Update::STATUS_UNAPPROVED)->setId($update->getUpdateId())->save();
        }
        
        /* The update changes was canceled now just update the approval of product back to approved.*/
        $product->setApproval(Approval::STATUS_APPROVED)->getResource()->saveAttribute($product, 'approval');
        if($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){
            $childProductCollection = $product->getTypeInstance()->getUsedProductCollection($product);
            foreach($childProductCollection as $childProduct){
                $childProduct->setApproval(Approval::STATUS_APPROVED)
                ->getResource()
                ->saveAttribute($childProduct, 'approval');
            }
        }
        
        $vendor = $this->_objectManager->create('Vnecoms\Vendors\Model\Vendor')->load($product->getVendorId());
        
        $this->_eventManager->dispatch(
            'vnecoms_vendors_push_notification',
            [
                'vendor_id' => $vendor->getId(),
                'type' => 'product_approval',
                'message' => __('Updates of %1 are rejected', '<strong>'.$product->getName().'</strong>'),
                'additional_info' => ['id' => $product->getId()],
            ]
        );
        
        /* Send Update product unapproved notification email*/
        $this->productHelper->sendUpdateProductUnapprovedEmailToVendor($product, $vendor, $updateCollection);
        
        $this->messageManager->addSuccess(
            __('The product %1 is rejected', $product->getName())
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('vendors/catalog_product/index');
    }
}

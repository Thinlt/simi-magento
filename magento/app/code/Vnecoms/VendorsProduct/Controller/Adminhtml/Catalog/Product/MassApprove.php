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
use Vnecoms\VendorsProduct\Model\Product\Update as ProductUpdate;

class MassApprove extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Vnecoms\VendorsProduct\Helper\Data
     */
    protected $productHelper;
    
    /**
     * @var \Vnecoms\Vendors\Model\VendorFactory
     */
    protected $vendorFactory;


    /**
     * @var \Magento\Catalog\Api\CategoryLinkManagementInterface
     */
    protected $categoryLinkManagement;
    
    /**
     * @param Context $context
     * @param Builder $productBuilder
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Vnecoms\VendorsProduct\Helper\Data $productHelper,
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->productHelper = $productHelper;
        $this->vendorFactory = $vendorFactory;
        
        parent::__construct($context, $productBuilder);
    }

    /**
     * Approve Update Changes of an Exist product
     * @param \Magento\Catalog\Model\Product $product
     */
    protected function approveExistProduct(
        \Magento\Catalog\Model\Product $product,
        \Vnecoms\Vendors\Model\Vendor $vendor
    ) {
        $updateCollection = $this->_objectManager->create('Vnecoms\VendorsProduct\Model\ResourceModel\Product\Update\Collection');
        $updateCollection->addFieldToFilter('product_id', $product->getId())
            ->addFieldToFilter('status', ProductUpdate::STATUS_PENDING);
        
        foreach ($updateCollection as $update) {
            $productData = unserialize($update->getProductData());
            $checkIsCategories = false;
            foreach ($productData as $attributeCode => $value) {
                $product->setData($attributeCode, $value);
                if($attributeCode == "category_ids"){
                    $checkIsCategories = true;
                }
            }
            $update->setStatus(ProductUpdate::STATUS_APPROVED)->setId($update->getUpdateId())->save();
            $product->setStoreId($update->getStoreId())->save();

            if($checkIsCategories){
                $this->getCategoryLinkManagement()->assignProductToCategories(
                    $product->getSku(),
                    $product->getCategoryIds()
                );
            }
        }
        /*Send update product notification email*/
        $this->productHelper->sendUpdateProductApprovedEmailToVendor($product, $vendor, $updateCollection);
    }
    
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $productApproved = 0;
        $vendors = [];
        foreach ($collection->getItems() as $product) {
            $product->load($product->getId());
            $vendorId = $product->getVendorId();
            if (!$vendorId) {
                continue;
            }
            
            if (!isset($vendors[$vendorId])) {
                $vendor = $this->vendorFactory->create();
                $vendor->load($vendorId);
                $vendors[$vendorId] = $vendor;
            }
            
            $vendor = $vendors[$vendorId];
            
            /**
             * Approve Pending updates.
             */
            if ($product->getApproval() == Approval::STATUS_PENDING_UPDATE) {
                $this->approveExistProduct($product, $vendor);
                $message = __('Updates of %1 are approved', '<strong>'.$product->getName().'</strong>');
            } else {
                $this->productHelper->sendProductApprovedEmailToVendor($product, $vendor);
                $message = __('Product %1 is approved', '<strong>'.$product->getName().'</strong>');
            }
            
            $product->setApproval(Approval::STATUS_APPROVED)
                ->getResource()
                ->saveAttribute($product, 'approval');
            
            if($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){
                $childProductCollection = $product->getTypeInstance()->getUsedProductCollection($product);
                foreach($childProductCollection as $childProduct){
                    $childProduct->setApproval(Approval::STATUS_APPROVED)
                        ->getResource()
                        ->saveAttribute($childProduct, 'approval');
                }
            }
            
            $this->_eventManager->dispatch(
                'vnecoms_vendors_push_notification',
                [
                    'vendor_id' => $vendor->getId(),
                    'type' => 'product_approval',
                    'message' => $message,
                    'additional_info' => ['id' => $product->getId()],
                ]
            );
            
            $productApproved++;
        }
        
        $this->messageManager->addSuccess(
            __('A total of %1 product(s) have been approved.', $productApproved)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('vendors/catalog_product/index');
    }

    /**
     * @return \Magento\Catalog\Api\CategoryLinkManagementInterface
     */
    private function getCategoryLinkManagement()
    {
        if (null === $this->categoryLinkManagement) {
            $this->categoryLinkManagement = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Catalog\Api\CategoryLinkManagementInterface::class);
        }
        return $this->categoryLinkManagement;
    }
}

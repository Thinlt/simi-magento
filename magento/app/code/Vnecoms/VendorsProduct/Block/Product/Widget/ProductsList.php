<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsProduct\Block\Product\Widget;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Widget\Block\BlockInterface;

/**
 * Catalog Products List widget block
 * Class ProductsList
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
{

    /**
     * Prepare and return product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function createCollection()
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $vendorHelper = $object_manager->get('Vnecoms\Vendors\Helper\Data');
        $notActiveVendorIds = $vendorHelper->getNotActiveVendorIds();

        $productHelper = $object_manager->get('Vnecoms\VendorsProduct\Helper\Data');

        if($collection->isEnabledFlat()){
            $collection->getSelect()->where('approval IN (?)',$productHelper->getAllowedApprovalStatus());
            if(sizeof($notActiveVendorIds)){
                $collection->getSelect()->where('vendor_id NOT IN('.implode(",", $notActiveVendorIds).')');
            }
        }else{
            $collection->addAttributeToFilter('approval',['in' => $productHelper->getAllowedApprovalStatus()]);
            if(sizeof($notActiveVendorIds)){
                $collection->addAttributeToFilter('vendor_id',['nin' => $notActiveVendorIds]);
            }
        }

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam($this->getData('page_var_name'), 1));

        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);

        return $collection;
    }

}

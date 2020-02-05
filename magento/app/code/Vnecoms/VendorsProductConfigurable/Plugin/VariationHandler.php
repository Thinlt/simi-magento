<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProductConfigurable\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Vnecoms\VendorsProduct\Model\Source\Approval;
/**
 * Class PriceBackend
 *
 *  Make price validation optional for configurable product
 */
class VariationHandler extends \Magento\ConfigurableProduct\Model\Product\VariationHandler
{

    /**
     * @param \Magento\Catalog\Model\Product\Attribute\Backend\Price $subject
     * @param \Closure $proceed
     * @param @param \Magento\Catalog\Model\Product $parentProduct
     * @param array $productsData
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGenerateSimpleProducts(
        \Magento\ConfigurableProduct\Model\Product\VariationHandler $subject,
        \Closure $proceed,
        $parentProduct,
        $productsData
    ) {
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $store = $object_manager->get('\Magento\Store\Model\StoreManagerInterface');
        $helper = $object_manager->create('\Vnecoms\VendorsProduct\Helper\Data');

        $generatedProductIds = [];
        $productsData = $this->duplicateImagesForVariations($productsData);
        foreach ($productsData as $simpleProductData) {
            $newSimpleProduct = $this->productFactory->create();
            if (isset($simpleProductData['configurable_attribute'])) {
                $configurableAttribute = json_decode($simpleProductData['configurable_attribute'], true);
                unset($simpleProductData['configurable_attribute']);
            } else {
                throw new LocalizedException(__('Configuration must have specified attributes'));
            }
        
            $this->fillSimpleProductData(
                $newSimpleProduct,
                $parentProduct,
                array_merge($simpleProductData, $configurableAttribute)
            );

            $request = $object_manager->create('\Magento\Framework\App\Request\Http');

            $savedraft = $request->getParam('savedraft', false);

            if(!$helper->isNewProductsApproval()){
                $approval = Approval::STATUS_APPROVED;
            }else{
                if($savedraft){
                    $approvalParent = Approval::STATUS_NOT_SUBMITED;
                }else{
                    $approvalParent = Approval::STATUS_PENDING;
                }

                $approval = $parentProduct->getApproval() ? $parentProduct->getApproval() : $approvalParent;
            }

            $newSimpleProduct->setVendorId($parentProduct->getVendorId());
            $newSimpleProduct->setApproval($approval);
            $newSimpleProduct->setWebsiteIds($parentProduct->getWebsiteIds());
            $newSimpleProduct->save();
        
            $generatedProductIds[] = $newSimpleProduct->getId();
        }
        return $generatedProductIds;
    }
}



<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Model\Product\Option;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\HydratorPool;
use Magento\Framework\App\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Repository extends \Magento\Catalog\Model\Product\Option\Repository
{
    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Catalog\Api\Data\ProductCustomOptionInterface $option)
    {
        $request = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\App\Request\Http::class);

        $storeId = $request->getParam('store',0);

        $productSku = $option->getProductSku();
        if (!$productSku) {
            throw new CouldNotSaveException(__('ProductSku should be specified'));
        }
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->get($productSku,false,$storeId);
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);


        $option->setData('product_id', $product->getData($metadata->getLinkField()));
        $option->setData('store_id', $product->getStoreId());

        if ($option->getOptionId()) {
            $options = $product->getOptions();
            if (!$options) {
                $options = $this->getProductOptions($product);
            }

            $persistedOption = array_filter($options, function ($iOption) use ($option) {
                return $option->getOptionId() == $iOption->getOptionId();
            });
            $persistedOption = reset($persistedOption);

            if (!$persistedOption) {
                throw new NoSuchEntityException();
            }
            $originalValues = $persistedOption->getValues();
            $newValues = $option->getData('values');
            if ($newValues) {
                if (isset($originalValues)) {
                    $newValues = $this->markRemovedValues($newValues, $originalValues);
                }
                $option->setData('values', $newValues);
            }
        }
        $option->save();
        return $option;
    }

}
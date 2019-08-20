<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsApi\Model\Product\Bundle;

use Magento\Framework\Exception\LocalizedException;
use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Bundle\Model\OptionRepository;

class VendorProductOptionManagement implements \Vnecoms\VendorsApi\Api\VendorProductOptionManagementInterface
{
    /**
     * @var \Magento\Bundle\Model\OptionRepository
     */
    protected $optionRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ApiHelper
     */
    protected $helper;

    /**
     * @param $helper
     * @param \Magento\Bundle\Model\OptionRepository $optionRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ApiHelper $helper,
        \Magento\Bundle\Api\ProductOptionRepositoryInterface $optionRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->helper = $helper;
        $this->optionRepository = $optionRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function save($customerId, \Magento\Bundle\Api\Data\OptionInterface $option)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($option->getSku(), true);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized for product %1.', $option->getSku()));
        }

        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            throw new InputException(__('Only implemented for bundle product'));
        }
        return $this->optionRepository->save($product, $option);
    }

    /**
     * Update option for bundle product
     * @param int $customerId
     * @param int $optionId
     * @param \Magento\Bundle\Api\Data\OptionInterface $option
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function update($customerId, $optionId, \Magento\Bundle\Api\Data\OptionInterface $option)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($option->getSku(), true);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized for product %1.', $option->getSku()));
        }
        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            throw new LocalizedException(__('Only implemented for bundle product'));
        }

        $options = $this->optionRepository->getList($option->getSku());
        $optionIdProducts = [];
        foreach ($options as $option){
            $optionIdProducts[] = $option['option_id'];
        }
        if (!in_array($optionId, $optionIdProducts)){
            throw new LocalizedException(__('OptionId is incorect.'));
        }
        return $this->optionRepository->save($product, $option);
    }
}

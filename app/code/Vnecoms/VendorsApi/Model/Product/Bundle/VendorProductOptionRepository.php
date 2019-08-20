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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class VendorProductOptionRepository implements \Vnecoms\VendorsApi\Api\VendorProductOptionRepositoryInterface
{
    /**
     * @var ApiHelper
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Bundle\Model\OptionRepository
     */
    protected $optionRepository;

    /**
     * VendorProductOptionRepository constructor.
     * @param ApiHelper $helper
     * @param ProductRepositoryInterface $productRepository
     * @param OptionRepository $optionRepository
     */
    public function __construct(
        ApiHelper $helper,
        ProductRepositoryInterface $productRepository,
        OptionRepository $optionRepository
    ) {
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->optionRepository = $optionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function get($customerId, $sku, $optionId)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku, ['edit_mode' => true]);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized for product %1.', $sku));
        }
        $options = $this->optionRepository->getList($sku);
        $optionIdProducts = [];
        foreach ($options as $option){
            $optionIdProducts[] = $option['option_id'];
        }
        if (!in_array($optionId, $optionIdProducts)){
            throw new LocalizedException(__('OptionId is incorect.'));
        }

        return $this->optionRepository->get($sku, $optionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getList($customerId, $sku)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku, ['edit_mode' => true]);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized for product %1.', $sku));
        }
        return $this->optionRepository->getList($sku);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($customerId, \Magento\Bundle\Api\Data\OptionInterface $option)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku, ['edit_mode' => true]);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized for product %1.', $sku));
        }
        return $this->optionRepository->delete($option);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($customerId, $sku, $optionId)
    {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku, ['edit_mode' => true]);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized for product %1.', $sku));
        }

        $options = $this->optionRepository->getList($sku);
        $optionIdProducts = [];
        foreach ($options as $option){
            $optionIdProducts[] = $option['option_id'];
        }
        if (!in_array($optionId, $optionIdProducts)){
            throw new LocalizedException(__('OptionId is incorect.'));
        }

        return $this->optionRepository->deleteById($sku, $optionId);
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        $customerId,
        \Magento\Catalog\Api\Data\ProductInterface $product,
        \Magento\Bundle\Api\Data\OptionInterface $option
    ) {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();
        $product = $this->productRepository->get($sku, ['edit_mode' => true]);
        if ($product->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not authorized for product %1.', $sku));
        }
        return $this->optionRepository->save($product, $option);
    }
}

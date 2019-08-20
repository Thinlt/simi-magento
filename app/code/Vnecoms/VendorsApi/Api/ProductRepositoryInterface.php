<?php

namespace Vnecoms\VendorsApi\Api;

interface ProductRepositoryInterface
{
    /**
     * Get product list
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vnecoms\VendorsApi\Api\Data\Catalog\ProductSearchResultsInterface
     */
    public function getList($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    
    /**
     * Create product
     * @param int $customerId
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param bool $saveOptions
     * @param bool $saveDraft
     * @param int $storeId
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save($customerId, \Magento\Catalog\Api\Data\ProductInterface $product, $saveOptions = false, $saveDraft = false,$storeId = null);
    
    /**
     * @param int $customerId
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string[] $attributes
     * @param string $saveDraft
     * @param string $storeId
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function update($customerId, \Magento\Catalog\Api\Data\ProductInterface $product, $attributes, $saveDraft = false, $storeId = null);
    
    /**
     * Get info about product by product SKU
     *
     * @param string $sku
     * @param bool $editMode
     * @param int|null $storeId
     * @param bool $forceReload
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($sku, $editMode = false, $storeId = null, $forceReload = false);
    
    /**
     * Get info about product by product id
     *
     * @param int $productId
     * @param bool $editMode
     * @param int|null $storeId
     * @param bool $forceReload
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
    */
    public function getById($productId, $editMode = false, $storeId = null, $forceReload = false);
    
    /**
     * Delete product
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\StateException
    */
    public function delete(\Magento\Catalog\Api\Data\ProductInterface $product);
    
    /**
     * @param string $sku
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
    */
    public function deleteById($sku);

    /**
     * @param int $customerId
     * @param int[] $productIds
     * @return mixed
     */
    public function submit(
        $customerId,
        $productIds
    );
}

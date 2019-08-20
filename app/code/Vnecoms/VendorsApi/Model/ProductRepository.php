<?php

namespace Vnecoms\VendorsApi\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory as SearchResultFactory;
use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Vnecoms\VendorsProduct\Model\Source\Approval;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Vendor repository.
 */
class ProductRepository implements \Vnecoms\VendorsApi\Api\ProductRepositoryInterface
{
    /**
     * @var Product[]
     */
    protected $instances = [];
    
    /**
     * @var Product[]
    */
    protected $instancesById = [];
    
    /**
     * @var ApiHelper
     */
    protected $helper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Vnecoms\VendorsProduct\Helper\Data
     */
    protected $vendorProductHelper;
    
    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory;
    
    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;
    
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    
    /**
     * @var \Vnecoms\VendorsApi\Api\Data\Catalog\ProductSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    
    /**
     * @var int
     */
    private $cacheLimit = 0;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @var \Magento\Catalog\Api\CategoryLinkManagementInterface
     */
    protected $categoryLinkManagement;


    /**
     * These attribute will not be checked for approval
     *
     * @var array
     */
    protected $notCheckAttributes = [
        'affect_product_custom_options',
        'options',
        'shipping_product_rate',
        'product_has_weight'
    ];

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $_productPriceIndexerProcessor;

    /**
     * ProductRepository constructor.
     * @param ApiHelper $helper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Vnecoms\VendorsProduct\Helper\Data $vendorProductHelper
     * @param \Vnecoms\VendorsApi\Api\Data\Catalog\ProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface|null $collectionProcessor
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     * @param \Psr\Log\LoggerInterface $logger
     * @param int $cacheLimit
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\ProductOptions\Config $config
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionTypeInterfaceFactory $factory
     */
    public function __construct(
        ApiHelper $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Vnecoms\VendorsProduct\Helper\Data $vendorProductHelper,
        \Vnecoms\VendorsApi\Api\Data\Catalog\ProductSearchResultsInterfaceFactory $searchResultsFactory,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor = null,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        \Psr\Log\LoggerInterface $logger,
        $cacheLimit = 1000,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
    ) {
        $this->helper = $helper;
        $this->objectManager = $objectManager;
        $this->vendorProductHelper = $vendorProductHelper;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->productRepository = $productRepository;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        $this->logger = $logger;
        $this->cacheLimit = (int)$cacheLimit;
        $this->_productPriceIndexerProcessor = $productPriceIndexerProcessor;
        $this->collectionFactory = $collectionFactory;
    }


    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return mixed|\Vnecoms\VendorsApi\Api\Data\Catalog\ProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();

        $om = ObjectManager::getInstance();
        $filter  = $om->get('Magento\Framework\Api\Filter');
        $filter->setField('vendor_id');
        $filter->setValue($vendorId);
        $filter->setConditionType('eq');
        $filterGroup = $om->get('Magento\Framework\Api\Search\FilterGroup');
        $filterGroup->setFilters([$filter]);

        $filterGroups = $searchCriteria->getFilterGroups();
        $filterGroups[] = $filterGroup;
        $searchCriteria->setFilterGroups($filterGroups);
        
        return $this->_getList($searchCriteria);
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria){

        $om = ObjectManager::getInstance();
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $om->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $this->extensionAttributesJoinProcessor->process($collection);
        
        $collection->addAttributeToSelect('*');
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        $collection->joinField('qty',
                            'cataloginventory_stock_item',
                            'qty',
                            'product_id=entity_id',
                            null,
                            '{{table}}.stock_id=1',
                            'left');

        $this->collectionProcessor->process($searchCriteria, $collection);
        $collection->load();
        $collection->addCategoryIds();
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        $imageHelper = $om->get('\Magento\Catalog\Helper\Image');
        foreach ($collection->getItems() as $product) {
            if($product->getThumbnail()){
                $thumbnailUrl = $imageHelper->init($product, 'product_thumbnail_image')
                    ->setImageFile($product->getFile())
                    ->resize(100)
                    ->getUrl();
                $product->setThumbnailUrl($thumbnailUrl);
            }
            $this->cacheProduct(
                $this->getCacheKey(
                    [
                        false,
                        $product->hasData(\Magento\Catalog\Model\Product::STORE_ID) ? $product->getStoreId() : null
                    ]
                ),
                $product
            );
        }
        return $searchResult;
    }

    /**
     * @param int $customerId
     * @param int[] $productIds
     * @return int|mixed
     * @throws \Exception
     */
    public function submit(
        $customerId,
        $productIds
    ){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToFilter('entity_id', ['in' => $productIds])
            ->addAttributeToFilter('vendor_id', $vendor->getId());
        $count = 0;
        try {
            if($collection->count()){
                $vendorProductHelper =  $this->objectManager->get('Vnecoms\VendorsProduct\Helper\Data');
                foreach ($collection->getItems() as $product) {
                    $product->setApproval(\Vnecoms\VendorsProduct\Model\Source\Approval::STATUS_PENDING)
                        ->getResource()
                        ->saveAttribute($product, 'approval');
                    $vendorProductHelper->sendNewProductApprovalEmailToAdmin($product, $vendor);
                    $count++;
                }
                $this->_productPriceIndexerProcessor->reindexList($productIds);
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new \Exception(__('There is something wrong.'));
        }
        return $count;
    }

    /**
     * @param int $customerId
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param bool $saveOptions
     * @param bool $saveDraft
     * @param int $storeId
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function save(
        $customerId,
        \Magento\Catalog\Api\Data\ProductInterface $product,
        $saveOptions = false,
        $saveDraft = false,
        $storeId = null
    ) {
        try{
            $existProduct = $this->get($product->getSku());
            throw new LocalizedException(__('The product is already exist'));
        }catch (\Exception $e){
            $existProduct = false;
        }
        
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();

        /* --------------- Save New Product -----------------*/
        /*Unset all values from not allow attributes (if exist)*/
        foreach ($this->vendorProductHelper->getNotUsedVendorAttributes() as $attribute) {
            $product->unsetData($attribute);
        }
        $product->setData('vendor_id', $vendorId);
        if (!$this->vendorProductHelper->isNewProductsApproval()) {
            $product->setApproval(Approval::STATUS_APPROVED);
        }else{
            if ($saveDraft) {
                $product->setApproval(Approval::STATUS_NOT_SUBMITED);
            } else{
                $product->setApproval(Approval::STATUS_PENDING);
                /*Send new product approval notification email to admin*/
                $this->vendorProductHelper->sendNewProductApprovalEmailToAdmin($product, $vendor);
            }
        }

        return $this->productRepository->save($product, $saveOptions);
    }

    /**
     * @param int $customerId
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string[] $attributes
     * @param string $saveDraft
     * @param string $storeId
     * @throws LocalizedException
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function update(
        $customerId,
        \Magento\Catalog\Api\Data\ProductInterface $product,
        $attributes,
        $saveDraft = false,
        $storeId=null
    ){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        $vendorId = $vendor->getId();

        $om = ObjectManager::getInstance();
        
        $existProduct = $this->get($product->getSku());
        if($existProduct->getVendorId() != $vendorId){
            throw new LocalizedException(__('You are not permited to save product %1', $product->getSku()));
        }
        $existProduct = $om->create('Magento\Catalog\Model\Product')->load($existProduct->getId());

        $saveProductFlag = false;
        $changedData = $this->_getChangedData($product, $existProduct, $attributes);
        if ($this->vendorProductHelper->isUpdateProductsApproval()) {
            if (!in_array($existProduct->getApproval(), [Approval::STATUS_PENDING, Approval::STATUS_NOT_SUBMITED, Approval::STATUS_UNAPPROVED])) {
                if (sizeof($changedData)) {
                    /*Save changed data*/
                    $update = $om->create('Vnecoms\VendorsProduct\Model\Product\Update');

                    /*Check if there is an exist pending update*/
                    $collection = $update->getCollection()
                        ->addFieldToFilter('vendor_id', $vendorId)
                        ->addFieldToFilter('store_id', $storeId)
                        ->addFieldToFilter('product_id', $product->getId())
                        ->addFieldToFilter('status', \Vnecoms\VendorsProduct\Model\Product\Update::STATUS_PENDING);
                    if ($collection->count()) {
                        /*Update changed data*/
                        $update = $collection->getFirstItem();
                        $update->setProductData(serialize($changedData));
                        $update->setId($update->getUpdateId())->save();
                    } else {
                        $update->setData([
                            'vendor_id' => $vendorId,
                            'store_id' => $storeId,
                            'product_id' => $product->getId(),
                            'product_data' => serialize($changedData),
                            'status' => \Vnecoms\VendorsProduct\Model\Product\Update::STATUS_PENDING
                        ])->save();
                    }

                    if (!$saveDraft) {
                        $product->setApproval(Approval::STATUS_PENDING_UPDATE)
                            ->getResource()
                            ->saveAttribute($product, 'approval');
                        $this->vendorProductHelper->sendUpdateProductApprovalEmailToAdmin($product, $vendor);
                    }
                }
            } else {
                $saveProductFlag = true;
                if (!$saveDraft) {
                    if ($existProduct->getApproval() != Approval::STATUS_PENDING) {
                        $this->vendorProductHelper->sendUpdateProductApprovalEmailToAdmin($existProduct, $vendor);
                    }

                    $existProduct->setApproval(Approval::STATUS_PENDING)
                        ->getResource()
                        ->saveAttribute($existProduct, 'approval');
                }
            }
        } else {
            $saveProductFlag = true;
            if ($product->getApproval() == Approval::STATUS_PENDING_UPDATE) {
                $product->setApproval(Approval::STATUS_APPROVED);
            }
        }
        if($saveProductFlag){
            foreach($changedData as $attr=>$value){
                $existProduct->setData($attr, $value);
            }
            $existProduct->save();
        }
        return $this->get($product->getSku());
    }

    /**
     * @param string $sku
     * @param bool $editMode
     * @param null $storeId
     * @param bool $forceReload
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($sku, $editMode = false, $storeId = null, $forceReload = false)
    {
        return $this->productRepository->get($sku, $editMode, $storeId, $forceReload);
    }

    /**
     * @param int $productId
     * @param bool $editMode
     * @param null $storeId
     * @param bool $forceReload
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($productId, $editMode = false, $storeId = null, $forceReload = false)
    {
        return $this->productRepository->getById($productId, $editMode, $storeId, $forceReload);
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        return $this->productRepository->delete($product);
    }

    /**
     * @param string $sku
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById($sku)
    {
        return $this->productRepository->deleteById($sku);
    }
    

    /**
     * Get key for cache
     *
     * @param array $data
     * @return string
     */
    protected function getCacheKey($data)
    {
        $serializeData = [];
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $serializeData[$key] = $value->getId();
            } else {
                $serializeData[$key] = $value;
            }
        }
        $serializeData = $this->serializer->serialize($serializeData);
        return sha1($serializeData);
    }
    
    /**
     * Add product to internal cache and truncate cache if it has more than cacheLimit elements.
     *
     * @param string $cacheKey
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return void
     */
    private function cacheProduct($cacheKey, \Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $this->instancesById[$product->getId()][$cacheKey] = $product;
        $this->instances[$product->getSku()][$cacheKey] = $product;
    
        if ($this->cacheLimit && count($this->instances) > $this->cacheLimit) {
            $offset = round($this->cacheLimit / -2);
            $this->instancesById = array_slice($this->instancesById, $offset, null, true);
            $this->instances = array_slice($this->instances, $offset, null, true);
        }
    }
    
    /**
     * Retrieve collection processor
     *
     * @deprecated 101.1.0
     * @return CollectionProcessorInterface
     */
    private function getCollectionProcessor()
    {
        if (!$this->collectionProcessor) {
            $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Magento\Catalog\Model\Api\SearchCriteria\ProductCollectionProcessor'
            );
        }
        return $this->collectionProcessor;
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

    /**
     * Get changed data
     * 
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param \Magento\Catalog\Api\Data\ProductInterface $oldProduct
     * @param string[] $attributes
     * @return array
     */
    private function _getChangedData(
        \Magento\Catalog\Api\Data\ProductInterface $product,
        \Magento\Catalog\Api\Data\ProductInterface $oldProduct,
        $attributes
    ) {
        $changedData = [];
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $productAttrCollection = $om->create('Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection')
            ->addVisibleFilter();
        $notUsedProductAttr = $this->vendorProductHelper->getNotUsedVendorAttributes();

        foreach ($attributes as $attrCode) {
            if (in_array($attrCode, $notUsedProductAttr)) {
                continue;
            }

            $newData = $product->getData($attrCode);

            $changedData[$attrCode] = $newData;
        }
        return $changedData;
    }
   

    /**
     * @param $postData
     * @param $productId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function handleImageRemoveError($postData, $productId)
    {
        if (isset($postData['product']['media_gallery']['images'])) {
            $removedImagesAmount = 0;
            foreach ($postData['product']['media_gallery']['images'] as $image) {
                if (!empty($image['removed'])) {
                    $removedImagesAmount++;
                }
            }
            if ($removedImagesAmount) {
                $expectedImagesAmount = count($postData['product']['media_gallery']['images']) - $removedImagesAmount;
                $product = $this->productRepository->getById($productId);
                if ($expectedImagesAmount != count($product->getMediaGallery('images'))) {
                    $this->messageManager->addNotice(
                        __('The image cannot be removed as it has been assigned to the other image role')
                    );
                }
            }
        }
    }
}

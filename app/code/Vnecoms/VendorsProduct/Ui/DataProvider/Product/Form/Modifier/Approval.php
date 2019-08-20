<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Vnecoms\VendorsProduct\Helper\Data as VendorProductHelper;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;
use Magento\Framework\Stdlib\ArrayManager;
use Vnecoms\VendorsProduct\Model\Product\UpdateFactory;

/**
 * Data provider for "Customizable Options" panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Approval extends AbstractModifier
{
    /**
     * @var \Vnecoms\VendorsProduct\Helper\Data
     */
    protected $_vendorProductHelper;
    
    /**
     * Set collection factory
     *
     * @var AttributeSetCollectionFactory
     */
    protected $_attributeSetCollectionFactory;
    
    /**
     * @var LocatorInterface
     */
    protected $locator;
    
    /**
     * @var ArrayManager
     */
    protected $arrayManager;
    
    /**
     * @var UpdateFactory
     */
    protected $_updateFactory;
    
    /**
     * @var \Vnecoms\VendorsProduct\Model\ResourceModel\Product\Update\Collection
     */
    protected $_updateCollection;
    
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected $_attributeRepository;
    
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;
    
    /**
     * Constructor
     *
     * @param VendorProductHelper $vendorProductHelper
     * @return \Vnecoms\VendorsProduct\Ui\DataProvider\Product\Form\Modifier\VendorsProduct
     */
    public function __construct(
        LocatorInterface $locator,
        VendorProductHelper $vendorProductHelper,
        AttributeSetCollectionFactory $attributeSetCollectionFactory,
        ArrayManager $arrayManager,
        UpdateFactory $updateFactory,
        \Magento\Catalog\Model\Product\Attribute\Repository $attrRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->locator = $locator;
        $this->_vendorProductHelper = $vendorProductHelper;
        $this->_attributeSetCollectionFactory = $attributeSetCollectionFactory;
        $this->arrayManager = $arrayManager;
        $this->_updateFactory = $updateFactory;
        $this->_attributeRepository = $attrRepository;
        $this->_categoryFactory = $categoryFactory;
        return $this;
    }
    
    /**
     * @var array
     */
    protected $_meta = [];
    
    /**
     * Get Product
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProduct()
    {
        return $this->locator->getProduct();
    }
    
    /**
     * Get update collection
     *
     * @return \Vnecoms\VendorsProduct\Model\ResourceModel\Product\Update\Collection
     */
    public function getUpdateCollection()
    {
        if (!$this->_updateCollection) {
            $this->_updateCollection = $this->_updateFactory->create()->getCollection()
                ->addFieldToFilter('product_id', $this->getProduct()->getId())
                ->addFieldToFilter('store_id', $this->getProduct()->getStoreId())
                ->addFieldToFilter('status', \Vnecoms\VendorsProduct\Model\Product\Update::STATUS_PENDING);
        }
        
        return $this->_updateCollection;
    }
    public function modifyData(array $data)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();
        $updateCollection = $this->getUpdateCollection();
        
        if (!$productId || !$updateCollection->count()) {
            return $data;
        }
        
        $updateData = unserialize($updateCollection->getFirstItem()->getProductData());
        foreach ($updateData as $attr => $value) {
            $data[$productId]['product'][$attr] = $value;
        }
        
        return $data;
    }
    
    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        
        $this->_meta = $meta;
        $updateCollection = $this->getUpdateCollection();
        if ($updateCollection->count()) {
            $data = unserialize($updateCollection->getFirstItem()->getProductData());
            foreach ($data as $attr => $value) {
                $productAttrValue = $product->getData($attr);
                if(is_string($productAttrValue) && ($productAttrValue == $value)) continue;
                $this->customizeProductField($attr);
            }
        }
        return $this->_meta;
    }
    
    /**
     * Customize credit dropdown value field
     *
     * @return $this
     */
    protected function customizeProductField($fieldCode)
    {
        $fieldPath = $this->arrayManager->findPath(
            $fieldCode,
            $this->_meta,
            null,
            'children'
        );
    
        if ($fieldPath) {
            $fieldMeta = $this->arrayManager->get($fieldPath, $this->_meta);

            $attribute = $this->_attributeRepository->get($fieldCode);
            $product = $this->getProduct();
            $isOptionAttr = sizeof($attribute->getOptions()) > 0;
            $notes = [];
            if (isset($fieldMeta['arguments']['data']['config']['additionalInfo'])) {
                $notes[] = $fieldMeta['arguments']['data']['config']['additionalInfo'];
            }
            $notes[] = __('This attribute value is changed but has not been approved yet.');
            
            $attributeValue = $product->getData($fieldCode);
            
            if ($fieldCode =='category_ids') {
                $tmpValue = [];
                $categoryCollection = $this->_categoryFactory->create()
                ->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToFilter('entity_id', ['in' => $attributeValue]);
            
                foreach ($categoryCollection as $category) {
                    $tmpValue[] = $category->getName();
                }
                $attributeValue = implode(",", $tmpValue);
            } elseif (is_array($attributeValue)) {
                if ($fieldCode == "tier_price") {
                    $om = \Magento\Framework\App\ObjectManager::getInstance();
                    $tmpValue = [];

                    foreach ($attributeValue as $value) {
                        if ($value["website_id"] == 0) {
                            $websiteName = __("All Websites");
                        } else {
                            $websiteName =  $om->get('Magento\Store\Model\Website')->load($value["website_id"])->getName();
                        }
                        $groupName = $om->get('Magento\Customer\Model\Group')->load($value["cust_group"])->getName();
                        if (!isset($groupName)) {
                            $groupName = __("All Group");
                        }
                        $string = $websiteName." , ";
                        $string .= $groupName." , ";
                        $string .= $value["price_qty"]." , ";
                        $string .= $value["price"]." , ";
                        $tmpValue[] = trim(trim($string), ",")."\n";
                    }
                    $attributeValue = implode("|", $tmpValue);
                } else {
                    $tmpValue = [];
                    foreach ($attributeValue as $value) {
                        $tmpValue[] = $attribute->getSource()->getOptionText($value);
                    }
                    $attributeValue = implode(",", $tmpValue);
                }
            } else {
                $attributeValue = $isOptionAttr?$product->getAttributeText($fieldCode):$attributeValue;
            }
            
            $notes[] = __('Current working value is: %1', '<strong>'.$attributeValue.'</strong>');
            
            $fieldMeta['arguments']['data']['config']['additionalInfo'] = '<div class="update-pending-approval">'.implode('<br />', $notes).'</div>';

            $this->_meta = $this->arrayManager->merge(
                $fieldPath,
                $this->_meta,
                $fieldMeta
            );
        }
    
        return $this;
    }
}

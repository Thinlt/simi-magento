<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Block\Adminhtml\PendingProduct;

/**
 * CMS block edit form container
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected $_attributeRepository;
    
    /**
     * @var array
     */
    protected $_attributes=[];
    
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;
    
    /**
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $attrRepository
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\Attribute\Repository $attrRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_attributeRepository = $attrRepository;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Vnecoms_VendorsProduct';
        $this->_controller = 'adminhtml_pendingProduct';

        parent::_construct();
        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('save');
        $this->addButton(
            'reject',
            [
                'label' => __('Reject Changes'),
                'class' => 'delete',
                'onclick' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getRejectUrl() . '\')'
            ]
        );
        $this->addButton(
            'approve',
            [
                'label' => __('Approve Changes'),
                'class' => 'save primary',
                'onclick' => 'setLocation(\'' . $this->getApproveUrl() . '\')',
            ]
        );
    }

    /**
     * Get edit form container header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __("Review Product '%1'", $this->escapeHtml($this->_coreRegistry->registry('current_product')->getName()));
    }
    
    /**
     * Get All updates
     *
     * @return \Vnecoms\VendorsProduct\Model\ResourceModel\Product\Update\Collection
     */
    public function getUpdates()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $updateCollection = $om->create('Vnecoms\VendorsProduct\Model\ResourceModel\Product\Update\Collection');
        $updateCollection->addFieldToFilter('product_id', $this->_coreRegistry->registry('current_product')->getId())
            ->addFieldToFilter('status', \Vnecoms\VendorsProduct\Model\Product\Update::STATUS_PENDING);
        return $updateCollection;
    }
    
    /**
     * Get Product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }
    
    /**
     * Get product attribute
     *
     * @param string $attributeCode
     * @return Ambigous <\Magento\Catalog\Api\Data\ProductAttributeInterface, \Magento\Eav\Api\Data\AttributeInterface>
     */
    public function getProductAttribute($attributeCode)
    {
        if (!isset($this->_attributes[$attributeCode])) {
            $this->_attributes[$attributeCode] = $this->_attributeRepository->get($attributeCode);
        }
        return $this->_attributes[$attributeCode];
    }
    
    /**
     * Get product attribute value
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $attributeCode
     * @param string $attributeValue
     * @return Ambigous <string, \Magento\Framework\Model\mixed, multitype:, boolean, unknown>
     */
    public function getProductAttributeValue(\Magento\Catalog\Model\Product $product, $attributeCode, $attributeValue = false)
    {
        $attribute = $this->_attributeRepository->get($attributeCode);
        $isOptionAttr = sizeof($attribute->getOptions()) > 0;
        
        if ($attributeValue ===false) {
            if (in_array($attributeCode, ['category_ids'])) {
                $attributeValue = $product->getData($attributeCode);
            } else {
                 $attributeValue = $isOptionAttr ? $product->getResource()->getAttributeRawValue($product->getId(), $attributeCode, $product->getStoreId()) : $product->getData($attributeCode);
            }
        }
        
        if ($attributeCode =='category_ids') {
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
            if ($attributeCode == "tier_price") {
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
            $attributeValue = $isOptionAttr?$product->getResource()->getAttribute($attributeCode)->getSource()->getOptionText($attributeValue):$attributeValue;
        }
        
        return $attributeValue;
    }
    
    /**
     * @return string
     */
    public function getRejectUrl()
    {
        return $this->getUrl('*/*/reject', [$this->_objectId => $this->getRequest()->getParam($this->_objectId)]);
    }
    /**
     * @return string
     */
    public function getApproveUrl()
    {
        return $this->getUrl('*/*/approve', [$this->_objectId => $this->getRequest()->getParam($this->_objectId)]);
    }
}

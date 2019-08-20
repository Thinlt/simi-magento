<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\VendorsCredit\Model\CreditProcessor\OrderPayment;
use Vnecoms\VendorsCredit\Model\CreditProcessor\ItemCommission;

class PendingAttributeUpdate implements ObserverInterface
{

    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected $_attributeRepository;
    
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;
    
    /**
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\Attribute\Repository $attrRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->_attributeRepository = $attrRepository;
        $this->_categoryFactory = $categoryFactory;
    }
    
    
    /**
     * Add multiple vendor order row for each vendor.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $form       = $observer->getForm();
        $product    = $this->getProduct();
        $update     = $this->_objectManager->create('Vnecoms\VendorsProduct\Model\Product\Update');

        /*Check if there is an exist pending update*/
        $collection = $update->getCollection()
            ->addFieldToFilter('product_id', $product->getId())
            ->addFieldToFilter('store_id', $product->getStoreId())
            ->addFieldToFilter('status', \Vnecoms\VendorsProduct\Model\Product\Update::STATUS_PENDING);
        if ($collection->count()) {
            $data = unserialize($collection->getFirstItem()->getProductData());
            foreach ($data as $attr => $value) {
                $element = $form->getElement($attr);
                if ($element) {
                    $attribute = $this->_attributeRepository->get($attr);
                    $isOptionAttr = sizeof($attribute->getOptions()) > 0;
                    $notes = [];
                    if ($element->getData('note')) {
                        $notes[] = $element->getData('note');
                    }
                    $notes[] = __('This attribute value is changed but has not been approved yet.');
                    $attributeValue = $product->getData($attr);
                    
                    if ($attr =='category_ids') {
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
                        $tmpValue = [];
                        foreach ($attributeValue as $value) {
                            $tmpValue[] = $attribute->getSource()->getOptionText($value);
                        }
                        $attributeValue = implode(",", $tmpValue);
                    } else {
                        $attributeValue = $isOptionAttr?$product->getAttributeText($attr):$attributeValue;
                    }
                    
                    $notes[] = __('Current working value is: %1', '<strong>'.$attributeValue.'</strong>');
                    
                    $element->setData('note', '<div class="update-pending-approval">'.implode('<br />', $notes).'</div>');
                    $element->setValue($value);
                }
            }
            //var_dump($data);exit;
        }
    }
    
    /**
     * Get Current Product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }
}

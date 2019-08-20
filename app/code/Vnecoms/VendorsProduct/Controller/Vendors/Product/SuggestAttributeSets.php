<?php

namespace Vnecoms\VendorsProduct\Controller\Vendors\Product;

use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

class SuggestAttributeSets extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_Vendors::catalog_product';
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Catalog\Model\Product\AttributeSet\SuggestedSet
     */
    protected $suggestedSet;

    /**
     * @var  \Vnecoms\VendorsProduct\Helper\Data
     */
    protected $productHelper;
    
    /**
     * Constructor
     *
     * @param \Vnecoms\Vendors\App\Action\Context $context
     * @param \Vnecoms\Vendors\App\ConfigInterface $config
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Catalog\Model\Product\AttributeSet\SuggestedSet $suggestedSet
     */

    public function __construct(
        \Vnecoms\Vendors\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\Product\AttributeSet\SuggestedSet $suggestedSet,
        \Vnecoms\VendorsProduct\Helper\Data $productHelper
    ) {
        parent::__construct($context);
        $this->resultJsonFactory    = $resultJsonFactory;
        $this->suggestedSet         = $suggestedSet;
        $this->productHelper        = $productHelper;
    }
    

    /**
     * Action for attribute set selector
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $result = $this->suggestedSet->getSuggestedSets($this->getRequest()->getParam('label_part'));
        foreach ($result as $index => $setInfo) {
            if (isset($setInfo['id']) && in_array($setInfo['id'], $this->productHelper->getAttributeSetRestriction())) {
                unset($result[$index]);
            }
        }
        $resultJson->setData(
            array_values($result)
        );
        return $resultJson;
    }
}

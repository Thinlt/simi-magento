<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Vendor tabs
 */
namespace Vnecoms\Vendors\Block\Adminhtml\Vendor\Edit\Tab\Info;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Default Attribute Tab Block
     *
     * @var string
     */
    protected $_attributeTabBlock = 'Vnecoms\Vendors\Block\Adminhtml\Vendor\Edit\Tab\Info\Attributes';

    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::widget/tabshoriz.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * Catalog helper
     *
     * @var \Magento\Catalog\Helper\Catalog
     */
    protected $_helperCatalog = null;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory
     */
    protected $_collectionFactory;

    
    /**
     *
     * @var \Vnecoms\Vendors\Model\ResourceModel\Vendor\Fieldset\Collection
     */
    protected $_fieldsetCollection;
    
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Helper\Catalog $helperCatalog
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        \Vnecoms\Vendors\Model\ResourceModel\Vendor\Fieldset\Collection $fieldsetCollection,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_coreRegistry = $registry;
        $this->_fieldsetCollection = $fieldsetCollection;
        $this->_fieldsetCollection->addFieldToFilter('form', \Vnecoms\Vendors\Helper\Data::PROFILE_FORM);
        $this->_fieldsetCollection->setOrder("sort_order", 'ASC');
        
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Initialize Tabs
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('vendor_info_tabs');
        $this->setDestElementId('vendor_info_tab_content');
        $this->setTitle(__('Vendor Data'));
    }

    /**
     * Prepare Layout Content
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareLayout()
    {
        foreach ($this->_fieldsetCollection as $fieldset) {
            $this->addTab(
                'vendor_fieldset_'.$fieldset->getId(),
                [
                    'label' => $fieldset->getTitle(),
                    'content' => $this->getLayout()->createBlock(
                        $this->_attributeTabBlock,
                        'fieldset'.$fieldset->getId().'.attribute'
                    )->setVendorFieldset($fieldset)->toHtml()
                ]
            );
        }
        return parent::_prepareLayout();
    }
}

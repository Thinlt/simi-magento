<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Vendor tabs
 */
namespace Vnecoms\VendorsDashboard\Block\Adminhtml\Dashboard\SellerState;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
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
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        
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
        $this->setId('vendor_dashboard_sellerstate');
        $this->setDestElementId('vendor_dashboard_seller_state_content');
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
        $this->addTab(
            'order_grid',
            [
                'label' => __('Last Orders'),
                'content' => $this->getLayout()->createBlock(
                    'Vnecoms\VendorsDashboard\Block\Adminhtml\Dashboard\Order\Grid'
                )->toHtml(),
                'active' => true
            ]
        );
        // load other tabs with ajax
        $this->addTab(
            'transaction_grid',
            [
                'label' => __('Last Transactions'),
                'url' => $this->getUrl('vendors/dashboard/lastTransaction', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
        
        $this->addTab(
            'bestseller_grid',
            [
                'label' => __('Bestseller'),
                'url' => $this->getUrl('vendors/dashboard/bestseller', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
        
        $this->addTab(
            'mostviewed_grid',
            [
                'label' => __('Most Viewed Products'),
                'url' => $this->getUrl('vendors/dashboard/mostViewed', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
        return parent::_prepareLayout();
    }
}

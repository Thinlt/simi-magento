<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Vendor tabs
 */
namespace Vnecoms\VendorsDashboard\Block\Vendors\Dashboard\SellerState;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @var string
     */
    protected $_template = 'Vnecoms_Vendors::widget/tabshoriz.phtml';
    
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
        $this->setClass('nav-tabs-custom');
    }

    /**
     * Get Last Transaction URL
     *
     * @return string
     */
    public function getLastTransactionUrl()
    {
        return $this->getUrl('dashboard/grid/lastTransaction', ['_current' => true]);
    }
    
    /**
     * Get Bestseller URL
     *
     * @return string
     */
    public function getBestsellerUrl()
    {
        return $this->getUrl('dashboard/grid/bestseller', ['_current' => true]);
    }
    
    /**
     * Get Most Viewed URL
     *
     * @return string
     */
    public function getMostViewedUrl()
    {
        return $this->getUrl('dashboard/grid/mostViewed', ['_current' => true]);
    }
    
    /**
     * Get order tabs content
     *
     * @return string
     */
    public function getOrderTabContent()
    {
        return $this->getLayout()->createBlock(
            'Vnecoms\VendorsDashboard\Block\Vendors\Dashboard\Order\Grid'
        )->toHtml();
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
                'content' => $this->getOrderTabContent(),
                'active' => true
            ]
        );
        // load other tabs with ajax
        $this->addTab(
            'transaction_grid',
            [
                'label' => __('Last Transactions'),
                'url' => $this->getLastTransactionUrl(),
                'class' => 'ajax'
            ]
        );
        
        $this->addTab(
            'bestseller_grid',
            [
                'label' => __('Bestseller'),
                'url' => $this->getBestsellerUrl(),
                'class' => 'ajax'
            ]
        );
        
        $this->addTab(
            'mostviewed_grid',
            [
                'label' => __('Most Viewed Products'),
                'url' => $this->getMostViewedUrl(),
                'class' => 'ajax'
            ]
        );
        return parent::_prepareLayout();
    }
}

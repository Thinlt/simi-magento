<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Vendor Group Edit Block
 */
namespace Vnecoms\Vendors\Block\Vendors\Account;

class Edit extends \Vnecoms\Vendors\Block\Vendors\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Apply" button
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Vnecoms_Vendors';
        $this->_controller = 'vendors_account';
        
        parent::_construct();
        $this->updateButton('save', 'label', __('Save'));

        return $this;
    }

    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $vendor = $this->_coreRegistry->registry('current_vendor');
        if ($vendor->getId()) {
            return __("Edit Seller '%s'", $this->escapeHtml($vendor->getName()));
        } else {
            return __('New Seller');
        }
    }
    
/**
 * Get URL for back (reset) button
 *
 * @return string
 */
    public function getBackUrl()
    {
        return $this->getUrl('dashboard');
    }
}

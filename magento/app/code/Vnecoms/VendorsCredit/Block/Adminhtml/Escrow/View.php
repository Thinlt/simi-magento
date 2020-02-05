<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsCredit\Block\Adminhtml\Escrow;

/**
 * Vendor Notifications block
 */
class View extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Constructor
     * 
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }
    
    /**
     * Constructor
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Vnecoms_VendorsCredit';
        $this->_controller = 'adminhtml_escrow';
        $this->_mode = 'view';
    
        parent::_construct();
    
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
    
        if ($this->getEscrow()->canCancel()) {
            $this->buttonList->add(
                'release_payment',
                [
                    'label' => __("Release"),
                    'onclick' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getReleaseUrl() . '\')',
                    'class' => 'save primary release-request'
                ]
            );

            $this->buttonList->add(
                'cancel_payment',
                [
                    'label' => __("Cancel"),
                    'onclick' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getCancelUrl() . '\')',
                    'class' => 'cancel cancel-request'
                ]
            );
            
        }
    
    }

    
    /**
     * Get current withdrawal
     *
     * @return \Vnecoms\VendorsCredit\Model\Escrow
     */
    public function getEscrow(){
        return $this->_coreRegistry->registry('current_escrow');
    }
    
    /**
     * Get Back Url
     * 
     * @return string
     */
    public function getBackUrl(){
        return $this->getUrl('vendors/credit/pending/');
    }
    
    /**
     * Get Cancel URL
     * 
     * @return string
     */
    public function getCancelUrl(){
        return $this->getUrl('vendors/credit_escrow/cancel',['id' => $this->getEscrow()->getId()]);
    }
    
    /**
     * Get Cancel URL
     *
     * @return string
     */
    public function getReleaseUrl(){
        return $this->getUrl('vendors/credit_escrow/release',['id' => $this->getEscrow()->getId()]);
    }
    
    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $escrow = $this->_coreRegistry->registry('current_escrow');
        return __("Escrow Transaction '#%1'", $this->escapeHtml($escrow->getId()));
    }
}

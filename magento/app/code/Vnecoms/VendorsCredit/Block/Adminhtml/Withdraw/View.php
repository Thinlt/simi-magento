<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsCredit\Block\Adminhtml\Withdraw;

use \Vnecoms\VendorsCredit\Model\Withdrawal;

/**
 * Vendor Notifications block
 */
class View extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * @var string
     */
    protected $_template = 'Vnecoms_VendorsCredit::widget/form/container.phtml';

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
        $this->_controller = 'adminhtml_withdraw';
        $this->_mode = 'view';
    
        parent::_construct();
    
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
    
        if ($this->canCancel($this->getWithdrawal())) {

            $this->addButton(
                'complete',
                [
                    'label' => __('Mark as Completed'),
                    'class' => 'save primary',
                    'data_attribute' => [
                        'mage-init' => ['button' => ['event' => 'save', 'target' => '#edit_form']],
                    ]
                ]
            );
            
            $this->buttonList->add(
                'cancel_request',
                [
                    'label' => __("Cancel Withdrawal Request"),
                    //'onclick' => 'showModalBox()',
                    'class' => 'cancel cancel-request'
                ]
            );
        }
    
    }
    
    /**
     * Can cancel the withdrawal request
     * 
     * @param Withdrawal $withdrawal
     * @return boolean
     */
    public function canCancel(Withdrawal $withdrawal){
        return $withdrawal->getStatus() == Withdrawal::STATUS_PENDING;
    }
    
    /**
     * Get current withdrawal
     *
     * @return \Vnecoms\VendorsCredit\Model\Withdrawal
     */
    public function getWithdrawal(){
        return $this->_coreRegistry->registry('current_withdrawal');
    }
    
    /**
     * Get Back Url
     * 
     * @return string
     */
    public function getBackUrl(){
        return $this->getUrl('vendors/credit_withdrawal/');
    }
    
    /**
     * Get Cancel URL
     * 
     * @return string
     */
    public function getCancelUrl(){
        return $this->getUrl('vendors/credit_withdrawal/cancel',['id' => $this->getWithdrawal()->getId()]);
    }
    
    /**
     * Get Cancel URL
     *
     * @return string
     */
    public function getCompleteUrl(){
        return $this->getUrl('vendors/credit_withdrawal/complete',['id' => $this->getWithdrawal()->getId()]);
    }
    
    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $withdrawal = $this->_coreRegistry->registry('current_withdrawal');
        return __("Withdrawal Request '%1'", $this->escapeHtml($withdrawal->getId()));
    }
}

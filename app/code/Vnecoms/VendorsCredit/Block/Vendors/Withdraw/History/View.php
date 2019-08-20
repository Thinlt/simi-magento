<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsCredit\Block\Vendors\Withdraw\History;

use \Vnecoms\VendorsCredit\Model\Withdrawal;

/**
 * Vendor Notifications block
 */
class View extends \Vnecoms\Vendors\Block\Vendors\Widget\Form\Container
{
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_vendorSession;
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     * @var \Vnecoms\VendorsCredit\Model\Source\Status
     */
    protected $_withdrawalStatus;
    
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
        $this->_controller = 'vendors_withdraw_history';
        $this->_mode = 'view';
    
        parent::_construct();
    
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
        $this->setId('credit_withdrawal_view');

    
        if ($this->canCancel($this->getWithdrawal())) {
            $this->buttonList->add(
                'cancel_request',
                [
                    'label' => __("Cancel Withdrawal Request"),
                    'onclick' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getCancelUrl() . '\')',
                    'class' => 'btn btn-danger'
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
        return $this->getUrl('credit/withdraw/history');
    }
    
    /**
     * Get Cancel URL
     * 
     * @return string
     */
    public function getCancelUrl(){
        return $this->getUrl('credit/withdraw/cancel',['id' => $this->getWithdrawal()->getId()]);
    }
}

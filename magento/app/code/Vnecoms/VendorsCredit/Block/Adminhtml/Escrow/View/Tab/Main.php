<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsCredit\Block\Adminhtml\Escrow\View\Tab;

use Vnecoms\VendorsCredit\Model\Escrow;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface
{

    /**
     * @var \Vnecoms\VendorsCredit\Model\Source\Status
     */
    protected $_withdrawalStatus;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Convert\DataObject $objectConverter
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Vnecoms\VendorsCredit\Model\Source\Status $withdrawalStatus,
        array $data = []
    ) {
        $this->_withdrawalStatus = $withdrawalStatus;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare content for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabLabel()
    {
        return __('Escrow Transaction');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('Escrow Transaction');
    }

    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }

    
    /**
     * @return Form
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setUseContainer(true);
        
        $fieldset = $form->addFieldset('escrow_form', ['legend' => __('Escrow Transaction'), 'class' => 'withdrawal-form']);
        $fieldset->addType('status', 'Vnecoms\VendorsCredit\Block\Form\Element\Status');
        $fieldset->addType('price', 'Vnecoms\VendorsCredit\Block\Form\Element\Price');
        $fieldset->addType('invoice', 'Vnecoms\VendorsCredit\Block\Form\Element\Invoice');
        $fieldset->addType('order', 'Vnecoms\VendorsCredit\Block\Form\Element\Order');
        $fieldset->addField(
            'escrow_id',
            'label',
            ['name' => 'escrow_id', 'label' => __('ID #'), 'title' => __('ID #'),]
        );
        $fieldset->addField(
            'amount',
            'price',
            ['name' => 'amount', 'label' => __('Amount'), 'title' => __('Amount'),]
        );
        $fieldset->addField(
            'status',
            'status',
            ['name' => 'status', 'label' => __('Status'), 'title' => __('Status'),]
        );
        $fieldset->addField(
            'invoice_link',
            'invoice',
            ['name' => 'relation_id', 'label' => __('Invoice #'), 'title' => __('Invoice #'),]
        );
        $fieldset->addField(
            'order_link',
            'order',
            ['name' => 'relation_id', 'label' => __('Order #'), 'title' => __('Order #'),]
        );
        $fieldset->addField(
            'created_at',
            'label',
            ['name' => 'created_at', 'label' => __('Created At'), 'title' => __('Created At'),]
        );
/*         $fieldset->addField(
            'updated_at',
            'label',
            ['name' => 'updated_at', 'label' => __('Updated At'), 'title' => __('Updated At'),]
        ); */
        
        $escrow = $this->getEscrow();
        $values = $escrow->getData();
        $values['amount'] = $this->formatBaseCurrency($escrow->getAmount());
        
        $values['created_at'] = $this->formatDateTime($escrow->getCreatedAt(), \IntlDateFormatter::MEDIUM);
        $values['updated_at'] = $this->formatDateTime($escrow->getUpdatedAt(), \IntlDateFormatter::MEDIUM);
        $values['invoice_link'] = $escrow->getRelationId();
        $values['order_link'] = $escrow->getRelationId();
        
        
        $form->setValues($values);


        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    /**
     * Format date time
     *
     * @param unknown $dateTime
     * @return string
     */
    public function formatDateTime($dateTime)
    {
        return $this->formatDate($dateTime, \IntlDateFormatter::MEDIUM).' '.$this->formatTime($dateTime, \IntlDateFormatter::MEDIUM);
    }
    
    
    /**
     * Get current withdrawal
     *
     * @return \Vnecoms\VendorsCredit\Model\Escrow
     */
    public function getEscrow()
    {
        return $this->_coreRegistry->registry('current_escrow');
    }
    
    /**
     * Format base currency
     *
     * @param float $amount
     * @return string
     */
    public function formatBaseCurrency($amount)
    {
        return $this->_storeManager->getStore()->getBaseCurrency()
        ->formatPrecision($amount, 2, [], false);
    }
    
    /**
     * Get Withdrawal Status Label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $status = $this->_withdrawalStatus->getOptionArray();
        return $status[$this->getWithdrawal()->getStatus()];
    }
}

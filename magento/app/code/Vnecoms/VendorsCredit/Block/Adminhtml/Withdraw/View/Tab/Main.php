<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsCredit\Block\Adminhtml\Withdraw\View\Tab;

use Vnecoms\VendorsCredit\Model\Withdrawal;
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
        return __('Withdrawal Request');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('Withdrawal Request');
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

       // $form->setUseContainer(true);
        $withdrawal = $this->getWithdrawal();

        $fieldset = $form->addFieldset('withdrawal_form', ['legend' => __('Withdrawal Request'), 'class' => 'withdrawal-form']);


        if ($withdrawal->getId()) {
            $fieldset->addField('withdrawal_id', 'hidden', ['name' => 'withdrawal_id']);

        }

        $fieldset->addType('status', 'Vnecoms\VendorsCredit\Block\Form\Element\Status');
        $fieldset->addType('price', 'Vnecoms\VendorsCredit\Block\Form\Element\Price');
        $fieldset->addField(
            'payment_method',
            'label',
            ['name' => 'payment_method', 'label' => __('Payment Method'), 'title' => __('Payment Method'),]
        );

        $fieldset->addField(
            'amount',
            'price',
            ['name' => 'amount', 'label' => __('Amount'), 'title' => __('Amount'),]
        );
        $fieldset->addField(
            'fee',
            'price',
            ['name' => 'fee', 'label' => __('Fee'), 'title' => __('Fee'),]
        );
        $fieldset->addField(
            'net_amount',
            'price',
            ['name' => 'net_amount', 'label' => __('Net Amount'), 'title' => __('Net Amount'),]
        );
        $fieldset->addField(
            'status',
            'status',
            ['name' => 'status', 'label' => __('Status'), 'title' => __('Status'),]
        );
        $fieldset->addField(
            'created_at',
            'label',
            ['name' => 'created_at', 'label' => __('Created At'), 'title' => __('Created At'),]
        );
        $fieldset->addField(
            'updated_at',
            'label',
            ['name' => 'updated_at', 'label' => __('Updated At'), 'title' => __('Updated At'),]
        );

        if($withdrawal->getStatus() == \Vnecoms\VendorsCredit\Model\Withdrawal::STATUS_PENDING){
            $fieldset->addField(
                'code_of_transfer',
                'text',
                ['name' => 'code_of_transfer', 'label' => __('Transaction Reference'), 'title' => __('Transaction Reference')]
            );
        }else{
            if($withdrawal->getStatus() == \Vnecoms\VendorsCredit\Model\Withdrawal::STATUS_CANCELED){
                $fieldset->addField(
                    'reason_cancel',
                    'label',
                    ['name' => 'reason_cancel', 'label' => __('Reason For'), 'title' => __('Reason For'),]
                );
            }else if($withdrawal->getData("code_of_transfer")){
                $fieldset->addField(
                    'code_of_transfer',
                    'label',
                    ['name' => 'code_of_transfer', 'label' => __('Transaction Reference'), 'title' => __('Transaction Reference'),]
                );
            }
        }


        $values = $withdrawal->getData();
        $values['payment_method'] = $this->getPaymentMethodTitle();
        $values['amount'] = $this->formatBaseCurrency($withdrawal->getAmount());
        $values['fee'] = '-'.$this->formatBaseCurrency($withdrawal->getFee());
        $values['net_amount'] = $this->formatBaseCurrency($withdrawal->getNetAmount());
        $values['created_at'] = $this->formatDateTime($withdrawal->getCreatedAt(), \IntlDateFormatter::MEDIUM);
        $values['updated_at'] = $this->formatDateTime($withdrawal->getUpdatedAt(), \IntlDateFormatter::MEDIUM);
        
        $fieldset = $form->addFieldset('additional_info', ['legend' => __('Additional Info'), 'class' => 'withdrawal-form']);
        foreach ($this->getAdditionalInfo() as $info) {
            $key = md5($info['label']);
            $fieldset->addField(
                $key,
                'label',
                ['name' => $key, 'label' => $info['label'], 'title' => $info['label']]
            );
            $values[$key] = $info['value'];
            //$field->setValue($info['value']);
        }
        
        
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
     * Get payment method title
     *
     * @return string
     */
    public function getPaymentMethodTitle()
    {
        return $this->_scopeConfig->getValue('withdrawal_methods/'.$this->getWithdrawal()->getMethod().'/title');
    }
    

    /**
     * Get current withdrawal
     *
     * @return \Vnecoms\VendorsCredit\Model\Withdrawal
     */
    public function getWithdrawal()
    {
        return $this->_coreRegistry->registry('current_withdrawal');
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
     * Get Additional Info
     *
     * @return array
     */
    public function getAdditionalInfo()
    {
        $additionalInfo = json_decode($this->getWithdrawal()->getAdditionalInfo(), true);
        return $additionalInfo?$additionalInfo:[];
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
    
    /**
     * Get status html class
     *
     * @return string
     */
    public function getStatusHtmlClass()
    {
        switch ($this->getWithdrawal()->getStatus()) {
            case Withdrawal::STATUS_PENDING:
                return 'label-warning';
            case Withdrawal::STATUS_COMPLETED:
                return 'label-success';
            case Withdrawal::STATUS_CANCELED:
                return 'label-default';
                /* case Withdrawal::STATUS_REJECTED:
                 return 'label-danger'; */
        }
    }
}

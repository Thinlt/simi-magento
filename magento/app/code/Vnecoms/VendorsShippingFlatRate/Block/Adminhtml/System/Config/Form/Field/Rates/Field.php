<?php

namespace Vnecoms\VendorsShippingFlatRate\Block\Adminhtml\System\Config\Form\Field\Rates;

class Field extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'system/config/rates.phtml';

    /**
     * Retrieve list of initial customer groups.
     *
     * @return array
     */
    protected function _getInitialCustomerGroups()
    {
        return [$this->_groupManagement->getAllCustomersGroup()->getId() => __('ALL GROUPS')];
    }

    /**
     * Sort values.
     *
     * @param array $data
     *
     * @return array
     */
    protected function _sortValues($data)
    {
        usort($data, [$this, '_sortTierPrices']);

        return $data;
    }

    /**
     * Sort tier price values callback method.
     *
     * @param array $a
     * @param array $b
     *
     * @return int
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _sortTierPrices($a, $b)
    {
        if ($a['credit_value'] != $b['credit_value']) {
            return $a['credit_value'] < $b['credit_value'] ? -1 : 1;
        }

        return 0;
    }

    /**
     * Prepare global layout
     * Add "Add tier" button to layout.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'label' => __('Add Rate'),
                'onclick' => 'return ratesControl.addItem()',
                'class' => 'btn btn-primary',
            ]
        );
        $button->setTemplate('Vnecoms_VendorsShippingFlatRate::widget/button.phtml');
        $button->setName('add_rate_button');

        $this->setChild('add_button', $button);

        return parent::_prepareLayout();
        \Magento\Backend\Block\Widget\Button;
    }

    /**
     * Prepare group price values.
     *
     * @return array
     */
    public function getValues()
    {
        $values = [];
        $values = $this->getElement()->getValue();
        $values = unserialize($values);
        return $values ? $values : [];
    }



    /**
     * Get shipping method types
     *
     * @return multitype:multitype:string \Magento\Framework\Phrase
     */
    public function getMethodTypes()
    {
        return [
            ['label' => __("Per Order"), 'value' => 'O'],
            ['label' => __("Per Item"), 'value' => 'I'],
        ];
    }
    
    /**
     * Get Handling Types
     *
     * @return multitype:multitype:string \Magento\Framework\Phrase
     */
    public function getHandlingTypes()
    {
        return [
            ['label' => __("Fixed"), 'value' => 'F'],
            ['label' => __("Percent"), 'value' => 'P'],
        ];
    }
    
    /**
     * Retrieve 'add group price item' button HTML.
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
    
    /**
     * Get Base Currency Code
     * 
     * @return string
     */
    public function getBaseCurrencySymbol(){
        return $this->_storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
    }
}

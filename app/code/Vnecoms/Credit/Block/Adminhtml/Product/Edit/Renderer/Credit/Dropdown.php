<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Block\Adminhtml\Product\Edit\Renderer\Credit;

/**
 * Adminhtml tier price item renderer
 */
class Dropdown extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Group\AbstractGroup
{
    /**
     * @var string
     */
    protected $_template = 'catalog/product/edit/credit/dropdown.phtml';

    /**
     * Retrieve list of initial customer groups
     *
     * @return array
     */
    protected function _getInitialCustomerGroups()
    {
        return [$this->_groupManagement->getAllCustomersGroup()->getId() => __('ALL GROUPS')];
    }

    /**
     * Sort values
     *
     * @param array $data
     * @return array
     */
    protected function _sortValues($data)
    {
        usort($data, [$this, '_sortTierPrices']);
        return $data;
    }

    /**
     * Sort tier price values callback method
     *
     * @param array $a
     * @param array $b
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
     * Add "Add tier" button to layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            ['label' => __('Add Price'), 'onclick' => 'return tierPriceControl.addItem()', 'class' => 'add']
        );
        $button->setName('add_tier_price_item_button');

        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }
    
    /**
     * Prepare group price values
     *
     * @return array
     */
    public function getValues()
    {
        $values = [];
        $data = $this->getElement()->getValue();
 
        if (is_array($data)) {
            $values = $this->_sortValues($data);
        }
    
        $currency = $this->_localeCurrency->getCurrency($this->_directoryHelper->getBaseCurrencyCode());
    
        foreach ($values as &$value) {
            $value['readonly'] = $this->isShowWebsiteColumn() &&
            !$this->isAllowChangeWebsite();
            $value['credit_price'] = $currency->toCurrency(
                $value['credit_price'],
                ['display' => \Magento\Framework\Currency::NO_SYMBOL]
            );
        }
    
        return $values;
    }
    
}

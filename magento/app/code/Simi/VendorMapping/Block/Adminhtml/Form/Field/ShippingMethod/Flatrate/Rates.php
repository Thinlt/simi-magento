<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\VendorMapping\Block\Adminhtml\Form\Field\ShippingMethod\Flatrate;

class Rates extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * \Magento\Store\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Renderer select option column
     */
    protected $_selectRenderer;

    protected $_inputRenderer;


    protected function _getSelectRenderer()
    {
        if (!$this->_selectRenderer) {
            $this->_selectRenderer = $this->getLayout()->createBlock(
                \Magento\Framework\View\Element\Html\Select::class,
                '',
                ['data' => [
                    'name' => $this->_getCellInputElementName('type'),
                    'is_render_to_js_template' => true
                ]]
            );
        }
        return $this->_selectRenderer;
    }

    protected function _getInputRenderer()
    {
        if (!$this->_inputRenderer) {
            $this->_inputRenderer = $this->getLayout()->createBlock(
                \Simi\VendorMapping\Block\Adminhtml\Form\Field\Renderer\Input::class,
                '',
                ['data' => ['validate' => 'validate-digits']]
            );
            $this->_inputRenderer->setCurrency(
                $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol()
            );
        }
        return $this->_inputRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('identifier',      ['label' => __('Identifier'), 'style'=> 'width: 80px;']);
        $this->addColumn('title',           ['label' => __('Title'), 'style'=> 'width: 80px;']);
        $this->addColumn('type', [
            'label' => __('Type'), 
            'style'=> 'width: 80px;',
            'renderer' => $typeRenderer = $this->_getSelectRenderer()
        ]);
        $this->addColumn('price',           ['label' => __('Price'), 'style'=> 'width: 80px;', 
            'renderer' => $this->_getInputRenderer()
        ]);
        $this->addColumn('free_shipping',   ['label' => __('Free Shipping'), 'style'=> 'width: 80px;', 
            'renderer' => $this->_getInputRenderer()
        ]);
        $this->addColumn('sort_order',      [
            'label' => __('Sort Order'), 'style'=> 'width: 40px;',
            'class' => 'input-text validate-digits'
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Rate');

        $typeRenderer->setOptions([
            ['value' => 'O', 'label' => __('Per Order'), 'params' => []],
            ['value' => 'I', 'label' => __('Per Item'), 'params' => []],
        ]);
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getSelectRenderer()->calcOptionHash($row->getData('type'))] = 'selected="selected"';
        $row->setData('option_extra_attrs', $optionExtraAttr);
    }
}

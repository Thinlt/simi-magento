<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Widget\Form\Renderer\Fieldset;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Column extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var AbstractElement
     */
    protected $_element;

    protected $optionType;
    protected $categoryFactory;
    protected $variableFactory;

    /**
     * @var string
     */
    protected $_template = 'Vnecoms_PdfPro::widget/form/renderer/fieldset/column.phtml';

    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('\Magento\Backend\Block\Widget\Button')
            ->setData(array(
                'label' => __('Add Column'),
                'class' => 'add easypdf-add',
                'onclick' => 'return columnControl.addItem()',
            ));
        $button->setName('add_column_item_button');

        $this->setChild('add_button', $button);

        return parent::_prepareLayout();
    }

    public function __construct(
        \Vnecoms\PdfPro\Model\Source\Widget\Optiontype $optiontype,
        \Vnecoms\PdfPro\Model\CategoryFactory $categoryFactory,
        \Vnecoms\PdfPro\Model\VariableFactory $variableFactory,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->optionType = $optiontype;
        $this->variableFactory = $variableFactory;
        $this->categoryFactory = $categoryFactory;

        parent::__construct($context, $data);
    }

    /**
     * @return AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->_element = $element;

        return $this->toHtml();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getInitialOptions()
    {
        $groups = new \Magento\Framework\DataObject();
        $category = $this->categoryFactory->create()->load($this->processEditor(), 'code')->getData();
        $variable = $this->variableFactory->create()->getCollection()
            ->addFieldToFilter('category_id', $category['category_id'])->getData();

        $variable = new \Magento\Framework\DataObject($variable);

        $groups->setData('item', array('label' => 'Item', 'value' => $variable->getData()));
        $this->_eventManager->dispatch('ves_pdfpro_init_item_attribute_after', array('attributes' => $groups));

        return $groups;
    }

    public function getValues()
    {
        $data = $this->getElement()->getValue();

        return $data;
    }

    public function getEditor()
    {
        return $this->getElement()->getEditor();
    }

    public function processEditor()
    {
        $editor = $this->getEditor();
        $data = explode('_', $editor);

        return $data[3].'_item';
    }

    public function getOptionType()
    {
        return $this->optionType->toOptionArray();
    }
}

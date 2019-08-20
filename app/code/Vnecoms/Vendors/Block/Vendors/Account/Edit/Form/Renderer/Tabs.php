<?php

namespace Vnecoms\Vendors\Block\Vendors\Account\Edit\Form\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Vnecoms\AutoRelatedProduct\Model\App;

/**
 * Widget Instance page groups (predefined layouts group) to display on
 *
 * @method \Magento\Widget\Model\Widget\Instance getWidgetInstance()
 */
class Tabs extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var AbstractElement|null
     */
    protected $_element = null;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     * @var string
     */
    protected $_template = 'Vnecoms_Vendors::account/form/renderer/tabs.phtml';

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_productType;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $_conditions;
    
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\Product\Type $productType
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\App\Action\Context $appContex,
        \Magento\Catalog\Model\Product\Type $productType,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\ObjectManager\ObjectManager $objectManager,
        array $data = []
    ) {
        $this->_formFactory = $formFactory;
        $this->_productType = $productType;
        $this->_coreRegistry = $coreRegistry;
        $this->_objectManager = $appContex->getObjectManager();
        $this->_conditions = $conditions;
        
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $tabBlock = $this->getLayout()->createBlock('Vnecoms\Vendors\Block\Vendors\Account\Edit\Form\Tabs', 'tabs');
        $this->setChild('tabs', $tabBlock);
    }
    /**
     * Render given element (return html of element)
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Setter
     *
     * @param AbstractElement $element
     * @return $this
     */
    public function setElement(AbstractElement $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Getter
     *
     * @return AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }
}

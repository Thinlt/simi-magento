<?php
namespace Vnecoms\Vendors\Block\Adminhtml\Vendor\Edit\Tab;

use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Info extends Generic implements TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'Vnecoms_Vendors::vendor/edit/tab/info.phtml';
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    
   /**
    *
    * @param \Magento\Backend\Block\Template\Context $context
    * @param \Magento\Framework\Registry $registry
    * @param \Magento\Framework\Data\FormFactory $formFactory
    * @param array $data
    */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
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
        return __('Seller Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('Seller Information');
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
        return parent::_prepareForm();
    }
    
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
    
        $this->setChild(
            'tabs',
            $this->getLayout()->createBlock('Vnecoms\Vendors\Block\Adminhtml\Vendor\Edit\Tab\Info\Tabs', 'tabs')
        );
    
        return parent::_prepareLayout();
    }
}

<?php
namespace Vnecoms\VendorsConfig\Block\System\Config;

class Edit extends \Magento\Config\Block\System\Config\Edit
{
    const DEFAULT_VENDOR_SECTION_BLOCK = 'Vnecoms\VendorsConfig\Block\System\Config\Form';
    
    /**
     * Block template File
     *
     * @var string
     */
    protected $_template = 'Vnecoms_VendorsConfig::system/config/edit.phtml';

    /**
     * Prepare layout object
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function _prepareLayout()
    {
        $sectionName = $this->getRequest()->getParam('section');
        $section = $this->_configStructure->getElement($sectionName);
        $blockName = $section->getFrontendModel();
        if (empty($blockName)) {
            $blockName = self::DEFAULT_VENDOR_SECTION_BLOCK;
        }
        $this->setHeaderCss($section->getHeaderCss());
        $this->setTitle($section->getLabel());

        $this->getToolbar()->addChild(
            'save_btn',
            'Vnecoms\Vendors\Block\Vendors\Widget\Button',
            [
                'id' => 'save',
                'label' => __('Save Config'),
                'class' => 'save primary btn-success',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#config-edit-form']],
                ]
            ]
        );
        $this->setChild('form', $this->getLayout()->createBlock($blockName));
        return \Magento\Backend\Block\Widget::_prepareLayout();
    }

    /**
     * Retrieve config save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/index/save', ['_current' => true]);
    }
}

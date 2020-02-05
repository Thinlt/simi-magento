<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Key;

use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends FormContainer
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * constructor.
     *
     * @param Context  $context
     * @param Registry $registry
     * @param array    $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize author edit block.
     */
    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'VnEcoms_PdfPro';
        $this->_controller = 'adminhtml_key';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Template'));
        $this->buttonList->remove('delete');

        if ($this->coreRegistry->registry('key_data')
            && $this->coreRegistry->registry('key_data')->getId()) {
            $this->buttonList->add(
                'delete-pdf',
                array(
                    'label' => __('Delete'),
                    'class' => 'delete',
                    'on_click' => 'deleteConfirm(\''.__('Are you sure you want to do this?').'\', \''
                        .$this->getUrl('*/*/delete', array('id' => $this->getRequest()->getParam('id'))).'\')',
                ),
                -100
            );

            $duplicateUrl = $this->_urlBuilder->getUrl(
                'vnecoms_pdfpro/key/duplicate', // path to the duplicate action, string like 'module_route/controller/action'
                [
                    // our model's id, id - is parameter from the request
                    'id' => $this->getRequest()->getParam('id'),
                ]
            );
            $this->buttonList->add(
                'duplicate',
                [
                    'class' => 'save',
                    'label' => __('Duplicate Ttest'),
                    'onclick' => 'setLocation("' . $duplicateUrl . '")'
                ],
                12 // sort order
            );
        }


        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form',
                        ],
                    ],
                ],
            ],
            -100
        );
    }

    /**
     * Retrieve text for header element depending on loaded author.
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->coreRegistry->registry('key_data')
            && $this->coreRegistry->registry('key_data')->getId()) {
            return __("Edit Template '%s'", $this->escapeHtml($this->coreRegistry->registry('key_data')->getApiKey()));
        } else {
            return __('New Template');
        }
    }

    /**
     * Prepare layout.
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('key_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'key_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'key_content');
                }
            };
        ";

        return parent::_prepareLayout();
    }
}

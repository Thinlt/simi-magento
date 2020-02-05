<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Template\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Config\Model\Config\Source\Yesno as BooleanOptions;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class Form.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Form extends GenericForm implements TabInterface
{
    /**
     * Event manager.
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var WysiwygConfig
     */
    protected $wysiwygConfig;

    /**
     * @var BooleanOptions
     */
    protected $booleanOptions;

    /**
     * @param ManagerInterface $eventManager
     * @param WysiwygConfig    $wysiwygConfig
     * @param BooleanOptions   $booleanOptions
     * @param Context          $context
     * @param Registry         $registry
     * @param FormFactory      $formFactory
     * @param array            $data
     */
    public function __construct(
        //ManagerInterface $eventManager,
        WysiwygConfig $wysiwygConfig,
        BooleanOptions $booleanOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->booleanOptions = $booleanOptions;
      //  $this->eventManager = $eventManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \VnEcoms\PdfPro\Model\Key $model */
        $model = $this->_coreRegistry->registry('current_template');

        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Theme information'),
                'class' => 'fieldset-wide',
            ]
        );

      //  $fieldset->addType('file', 'Sample\News\Block\Adminhtml\Author\Helper\File');

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }
        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'class' => 'required-entry',
            ]
        );

        $fieldset->addType('preview', 'Vnecoms\PdfPro\Block\Adminhtml\Template\Helper\Preview');

        if ($model->getId()) {
            $fieldset->addField(
                'preview_image',
                'preview',
                [
                    'name' => 'preview_image',
                    'label' => __('Preview Image'),
                    'title' => __('Preview Image'),
                ]
            );
        }

        $fieldset->addType('attachments', 'Vnecoms\PdfPro\Block\Adminhtml\Template\Helper\Uploader');

        if (!$this->getRequest()->getParam('id')) {
            $fieldset->addField(
                'template',
                'attachments',
                [
                    'name' => 'template',
                    'label' => __('Package Theme Upload'),
                    'title' => __('Package Theme Upload'),
                    'required' => true,
                    'class' => 'required-entry',
                ]
            );

            $fieldset->addField(
                'upload_id',
                'hidden',
                ['name' => 'upload_id']
            );

            $fieldset->addField(
                'target_dir',
                'hidden',
                [
                    'name' => 'target_dir',
                ]
            );
        }

        $data = $this->_session->getData('template_data', true);
        if ($data) {
            $model->addData($data);
        }
        $form->addValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Theme Information');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs.
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden.
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}

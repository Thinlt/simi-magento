<?php

namespace Vnecoms\PageBuilder\Ui\Component\Form\Element;

use Magento\Framework\Data\FormFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;
use Vnecoms\PageBuilder\Helper\Data as Helper;

/**
 * Class ProductActions
 */
class Wysiwyg extends \Magento\Ui\Component\Form\Element\Wysiwyg
{
    /**
     * @var \Vnecoms\PageBuilder\Helper\Data
     */
    protected $helper;
    
    /**
     * @param ContextInterface $context
     * @param FormFactory $formFactory
     * @param ConfigInterface $wysiwygConfig
     * @param Helper $helper
     * @param array $components
     * @param array $data
     * @param array $config
     */
    public function __construct(
        ContextInterface $context,
        FormFactory $formFactory,
        ConfigInterface $wysiwygConfig,
        Helper $helper,
        array $components = [],
        array $data = [],
        array $config = []
    ) {
        $this->helper = $helper;
        
        $wysiwygConfigData = isset($config['wysiwygConfigData']) ? $config['wysiwygConfigData'] : [];
        $this->form = $formFactory->create();
        $this->editor = $this->form->addField(
            $context->getNamespace() . '_' . $data['name'],
            'Magento\Framework\Data\Form\Element\Editor',
            [
                'force_load' => true,
                'rows' => 20,
                'name' => $data['name'],
                'config' => $wysiwygConfig->getConfig($wysiwygConfigData),
                'wysiwyg' => isset($config['wysiwyg']) ? $config['wysiwyg'] : null,
            ]
        );
        $data['config']['content']          = $this->editor->getElementHtml();
        $data['config']['elementId']        = $this->editor->getHtmlId();
        
        return \Magento\Ui\Component\AbstractComponent::__construct($context, $components, $data);
    }
}

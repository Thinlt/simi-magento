<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProductConfigurable\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\Container;
use Magento\Framework\UrlInterface;

/**
 * Data provider for Attribute Set handler in the Configurable products
 */
class ConfigurableAttributeSetHandler extends AbstractModifier
{
    const ATTRIBUTE_SET_HANDLER_MODAL = 'configurable_attribute_set_handler_modal';
    const XML_PATH_NOT_ALLOWED_ATTRIBUTES = 'vendors/catalog/hide_configurable_attributes';
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        UrlInterface $urlBuilder,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->registry = $registry;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Remove not used attributes
     */
    public function removeConfigurableAttributes($meta)
    {
        if($this->registry->registry('product')->getTypeId() != 'configurable') return $meta;
        $notAllowedAttributes = explode(',',$this->scopeConfig->getValue(self::XML_PATH_NOT_ALLOWED_ATTRIBUTES));
        foreach ($meta as $groupCode => $group) {
            if (!isset($group['children'])) {
                continue;
            }
            $attributeContainers = $group['children'];
    
            foreach ($notAllowedAttributes as $attributeCode) {
                unset($meta[$groupCode]['children']['container_'.$attributeCode]);
            }
        }
        
        return $meta;
    }
    
    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->removeConfigurableAttributes($meta);
        
        $meta = array_merge_recursive(
            $meta,
            [
                self::ATTRIBUTE_SET_HANDLER_MODAL => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Modal::NAME,
                                'dataScope' => '',
                                'options' => [
                                    'title' => __('Choose Affected Attribute Set'),
                                    'type' => 'popup',
                                ],
                            ],
                        ],
                    ],
                    'children' => [
                        'affectedAttributeSetError' => $this->getAttributeSetErrorContainer(),
                        'affectedAttributeSetCurrent' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'componentType' => Form\Field::NAME,
                                        'formElement' => Form\Element\Checkbox::NAME,
                                        'prefer' => 'radio',
                                        'description' => __('Add configurable attributes to the current Attribute Set'),
                                        'dataScope' => 'configurableAffectedAttributeSet',
                                        'valueMap' => [
                                            'true' => 'current',
                                            'false' => '0',
                                        ],
                                        'value' => 'current',
                                        'sortOrder' => 20,
                                    ],
                                ],
                            ],
                        ],
                        /*
                        'affectedAttributeSetNew' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'componentType' => Form\Field::NAME,
                                        'formElement' => Form\Element\Checkbox::NAME,
                                        'prefer' => 'radio',
                                        'description' => __(
                                            'Add configurable attributes to the new Attribute Set based on current'
                                        ),
                                        'dataScope' => 'configurableAffectedAttributeSet',
                                        'valueMap' => [
                                            'true' => 'new',
                                            'false' => '0',
                                        ],
                                        'value' => '0',
                                        'sortOrder' => 30,
                                    ],
                                ],
                            ],
                        ],
                                    'configurableNewAttributeSetName' => $this->getNewAttributeSet(),
                          */
                        'affectedAttributeSetExisting' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'componentType' => Form\Field::NAME,
                                        'formElement' => Form\Element\Checkbox::NAME,
                                        'prefer' => 'radio',
                                        'description' => __(
                                            'Add configurable attributes to the existing Attribute Set'
                                        ),
                                        'dataScope' => 'configurableAffectedAttributeSet',
                                        'valueMap' => [
                                            'true' => 'existing',
                                            'false' => '0',
                                        ],
                                        'value' => '0',
                                        'sortOrder' => 50,
                                    ],
                                ],
                            ],
                        ],
                        'configurableExistingAttributeSetId' => $this->getExistingAttributeSet($meta),
                        'confirmButtonContainer' => $this->getConfirmButton(),
                    ],
                ],
            ]
        );

        return $meta;
    }

    /**
     * Returns confirm button configuration
     *
     * @return array
     */
    protected function getConfirmButton()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => 100,
                    ],
                ],
            ],
            'children' => [
                'confirm_button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => 'product_form.product_form.configurableVariations',
                                        'actionName' => 'addNewAttributeSetHandler',
                                    ],
                                ],
                                'title' => __('Confirm'),
                                'sortOrder' => 10
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns new attribute set input configuration
     *
     * @return array
     */
    protected function getNewAttributeSet()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataType' => Form\Element\DataType\Text::NAME,
                        'formElement' => Form\Element\Input::NAME,
                        'componentType' => Form\Field::NAME,
                        'dataScope' => 'configurableNewAttributeSetName',
                        'additionalClasses' => 'new-attribute-set-name',
                        'label' => __('New Attribute Set Name'),
                        'sortOrder' => 40,
                        'validation' => ['required-entry' => true],
                        'imports' => [
                            'visible' => 'ns = ${ $.ns }, index = affectedAttributeSetNew:checked',
                            'disabled' =>
                                '!ns = ${ $.ns }, index = affectedAttributeSetNew:checked',
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns configuration for existing attribute set options
     *
     * @param array $meta
     * @return null|array
     */
    protected function getExistingAttributeSet($meta)
    {
        $ret = null;
        if ($name = $this->getGeneralPanelName($meta)) {
            if (!empty($meta[$name]['children']['attribute_set_id']['arguments']['data']['config']['options'])) {
                $options = $meta[$name]['children']['attribute_set_id']['arguments']['data']['config']['options'];

                $attributesRestriction = \Magento\Framework\App\ObjectManager::getInstance()->get(
                    'Vnecoms\VendorsProduct\Helper\Data'
                )->getAttributeSetRestriction();
                $newOptions = [];
                foreach ($options as $option) {
                    if (in_array($option["value"], $attributesRestriction)) {
                        continue;
                    }
                    $newOptions[] = $option;
                }

                $ret = [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'component' => 'Magento_Ui/js/form/element/ui-select',
                                'disableLabel' => true,
                                'filterOptions' => false,
                                'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                'formElement' => 'select',
                                'componentType' => Form\Field::NAME,
                                'options' => $newOptions,
                                'label' => __('Choose existing Attribute Set'),
                                'dataScope' => 'configurableExistingAttributeSetId',
                                'sortOrder' => 60,
                                'multiple' => false,
                                'imports' => [
                                    'value' => 'ns = ${ $.ns }, index = attribute_set_id:value',
                                    'visible' => 'ns = ${ $.ns }, index = affectedAttributeSetExisting:checked',
                                    'disabled' =>
                                        '!ns = ${ $.ns }, index = affectedAttributeSetExisting:checked',
                                ],
                            ],
                        ],
                    ],
                ];
            }
        }

        return $ret;
    }

    /**
     * Returns configurations for the messages container
     *
     * @return array
     */
    protected function getAttributeSetErrorContainer()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Magento_Ui/js/form/components/html',
                        'componentType' => Container::NAME,
                        'content' => '',
                        'sortOrder' => 10,
                        'visible' => 0,
                    ],
                ],
            ],
        ];
    }
}

<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProductConfigurable\Block\Vendors\Product\Edit\Button;

use Magento\Ui\Component\Control\Container;
use Vnecoms\VendorsProduct\Block\Vendors\Product\Edit\Button\Generic as Generic;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Catalog\Model\Product\Type;

/**
 * Class Save
 */
class Save extends Generic
{
    /**
     * @var array
     */
    private static $availableProductTypes = [
        ConfigurableType::TYPE_CODE,
        Type::TYPE_SIMPLE,
        Type::TYPE_VIRTUAL
    ];

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if ($this->getProduct()->isReadonly()) {
            return [];
        }

        return [
            'label' => __('Save Product'),
            'class' => 'save primary btn btn-default btn-success btn-sm',
            'button_class' => 'fa fa-check-circle button-save',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => $this->getSaveTarget(),
                                'actionName' => $this->getSaveAction(),
                                'params' => [
                                    true,
                                    [
                                        'savedraft' => true,
                                        'back' => 'edit'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'class_name' => 'Vnecoms\Vendors\Block\Vendors\Widget\Button\SplitButton',
            'options' => $this->getOptions(),
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    protected function getOptions()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $helperData =  $om->create('Vnecoms\VendorsProduct\Helper\Data');
        if(
            ($helperData->isUpdateProductsApproval() && $this->getProduct()->getId()) ||
            ($helperData->isNewProductsApproval() && !$this->getProduct()->getId())
        ){
            $options[] = [
                'id_hard' => 'save_and_submit',
                'label' => __('Save & Submit for Approval'),
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => $this->getSaveTarget(),
                                    'actionName' => $this->getSaveAction(),
                                    'params' => [
                                        true,
                                        [
                                            'back' => 'edit'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ];
        }
        
        $options[] = [
            'id_hard' => 'save_and_new',
            'label' => __('Save & New'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => $this->getSaveTarget(),
                                'actionName' => $this->getSaveAction(),
                                'params' => [
                                    true,
                                    [
                                        'back' => 'new',
                                        'savedraft' => true
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        if (!$this->context->getRequestParam('popup') && $this->getProduct()->isDuplicable()) {
            $options[] = [
                'label' => __('Save & Duplicate'),
                'id_hard' => 'save_and_duplicate',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => $this->getSaveTarget(),
                                    'actionName' => $this->getSaveAction(),
                                    'params' => [
                                        true,
                                        [
                                            'back' => 'duplicate',
                                            'savedraft' => true
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ];
        }

        $options[] = [
            'id_hard' => 'save_and_close',
            'label' => __('Save & Close'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => $this->getSaveTarget(),
                                'actionName' => $this->getSaveAction(),
                                'params' => [
                                    true,
                                    [
                                        'savedraft' => true
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        return $options;
    }

    /**
     * Retrieve target for button
     * @return string
     */
    protected function getSaveTarget()
    {
        $target = 'product_form.product_form';
        if ($this->isConfigurableProduct()) {
            $target = 'product_form.product_form.configurableVariations';
        }
        return $target;
    }

    /**
     * Retrieve action for button
     * @return string
     */
    protected function getSaveAction()
    {
        $action = 'save';
        if ($this->isConfigurableProduct()) {
            $action = 'saveFormHandler';
        }
        return $action;
    }

    /**
     * @return boolean
     */
    protected function isConfigurableProduct()
    {
        return in_array($this->getProduct()->getTypeId(), self::$availableProductTypes);
    }
}

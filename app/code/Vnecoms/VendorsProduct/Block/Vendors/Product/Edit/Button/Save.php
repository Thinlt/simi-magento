<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Block\Vendors\Product\Edit\Button;

use Magento\Ui\Component\Control\Container;

/**
 * Class Save
 */
class Save extends Generic
{
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
                                'targetName' => 'product_form.product_form',
                                'actionName' => 'save',
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
            'class_name' => Container::SPLIT_BUTTON,
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
        ) {

            $options[] = [
                'id_hard' => 'save_and_draft',
                'label' => __('Save & Submit for Approval'),
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'product_form.product_form',
                                    'actionName' => 'save',
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
                                'targetName' => 'product_form.product_form',
                                'actionName' => 'save',
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
                                    'targetName' => 'product_form.product_form',
                                    'actionName' => 'save',
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
                                'targetName' => 'product_form.product_form',
                                'actionName' => 'save',
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
}

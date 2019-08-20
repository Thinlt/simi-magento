<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Ui\DataProvider\Key\Form\Modifier;

use Magento\Ui\Component\Form;
use Magento\Framework\Phrase;

/**
 * Data provider for main panel of product page.
 */
class General implements \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    const FIELDSET_CODE = 'general';

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                'general' => [
//                    'children' => [
//                        'chooser' => $this->getChooserFieldset(),
//                    ],
                ],
            ]
        );

        return $meta;
    }

    public function getChooserFieldset()
    {
        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    __('test'),
                    __('Add Related Products'),
                    'chooser'
                ),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Related Products'),
                        'collapsible' => false,
                        'componentType' => 'fieldset',
                        'dataScope' => '',
                        'sortOrder' => 10,
                    ],
                ],
            ],
        ];
    }

    /**
     * Retrieve button set.
     *
     * @param Phrase $content
     * @param Phrase $buttonTitle
     * @param string $scope
     *
     * @return array
     */
    protected function getButtonSet(Phrase $content, Phrase $buttonTitle, $scope)
    {
        $modalTarget = 'chooser'.'.'.$scope.'.modal';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => $content,
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'button_'.$scope => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $modalTarget.'.'.$scope.'_product_listing',
                                        'actionName' => 'render',
                                    ],
                                ],
                                'title' => $buttonTitle,
                                'provider' => null,
                            ],
                        ],
                    ],

                ],
            ],
        ];
    }
}

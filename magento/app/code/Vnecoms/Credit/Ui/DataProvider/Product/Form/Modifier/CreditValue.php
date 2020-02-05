<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Price;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Vnecoms\Credit\Model\Source\Type as CreditType;

/**
 * Data provider for categories field of product page
 */
class CreditValue extends AbstractModifier
{


    /**
     * @var DbHelper
     */
    protected $dbHelper;


    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @param LocatorInterface $locator
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param DbHelper $dbHelper
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        DbHelper $dbHelper,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->dbHelper = $dbHelper;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        
        $this->customizeCreditDropdownValueField();
        $this->customizeCreditCustomValueField();

        $this->customizeCreditTypeField();
        $this->customizeCreditRateField();
        $this->customizeCreditFixedValueField();
        
        return $this->meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Customize credit dropdown value field
     *
     * @return $this
     */
    protected function customizeCreditDropdownValueField()
    {
        $fieldCode = 'credit_value_dropdown';
        $creditValueDropdownPath = $this->arrayManager->findPath(
            $fieldCode,
            $this->meta,
            null,
            'children'
        );
    
        if ($creditValueDropdownPath) {
            $this->meta = $this->arrayManager->merge(
                $creditValueDropdownPath,
                $this->meta,
                $this->getCreditDropdownStruct($creditValueDropdownPath)
            );
            
            $this->meta = $this->arrayManager->set(
                $this->arrayManager->slicePath($creditValueDropdownPath, 0, -3)
                . '/' . $fieldCode,
                $this->meta,
                $this->arrayManager->get($creditValueDropdownPath, $this->meta)
            );
            
            $this->meta = $this->arrayManager->remove(
                $this->arrayManager->slicePath($creditValueDropdownPath, 0, -2),
                $this->meta
            );
        }
    
        return $this;
    }

    /**
     * Customize credit custom value field
     *
     * @return $this
     */
    protected function customizeCreditCustomValueField()
    {
        $fieldCode = 'container_credit_value_custom';
        $fieldPath = $this->arrayManager->findPath(
            $fieldCode,
            $this->meta,
            null,
            'children'
        );
    
        $this->meta = $this->arrayManager->replace(
            $fieldPath,
            $this->meta,
            $this->getCreditValueCustomStruct($fieldPath)
        );
        return $this;
    }
    
    /**
     * Customize credit custom value field
     *
     * @return \Vnecoms\Credit\Ui\DataProvider\Product\Form\Modifier\CreditValue
     */
    protected function customizeCreditTypeField()
    {
        $fieldCode = 'container_credit_type';
        $fieldPath = $this->arrayManager->findPath(
            $fieldCode,
            $this->meta,
            null,
            'children'
        );
    
        $this->meta = $this->arrayManager->replace(
            $fieldPath,
            $this->meta,
            $this->getCreditTypeStruct($fieldPath)
        );
        return $this;
    }
    /**
     * Customize credit rate field
     * 
     * @return \Vnecoms\Credit\Ui\DataProvider\Product\Form\Modifier\CreditValue
     */
    protected function customizeCreditRateField(){
        $fieldCode = 'container_credit_rate';
        $fieldPath = $this->arrayManager->findPath(
            $fieldCode,
            $this->meta,
            null,
            'children'
        );
        
        $this->meta = $this->arrayManager->replace(
            $fieldPath,
            $this->meta,
            $this->getCreditRateStruct($fieldPath)
        );
        return $this;
    }
    
    /**
     * Customize credit fixed value field
     * @return \Vnecoms\Credit\Ui\DataProvider\Product\Form\Modifier\CreditValue
     */
    protected function customizeCreditFixedValueField(){
        $fieldCode = 'container_credit_value_fixed';
        $fieldPath = $this->arrayManager->findPath(
            $fieldCode,
            $this->meta,
            null,
            'children'
        );
    
        $this->meta = $this->arrayManager->replace(
            $fieldPath,
            $this->meta,
            $this->getCreditFixedValueStruct($fieldPath)
        );
        
        
        $fieldCode = 'container_credit_price';
        $fieldPath = $this->arrayManager->findPath(
            $fieldCode,
            $this->meta,
            null,
            'children'
        );
        
        $this->meta = $this->arrayManager->replace(
            $fieldPath,
            $this->meta,
            $this->getCreditFixedValueStruct($fieldPath)
        );
        
        return $this;
    }
    
    
    /**
     * Get credit dropdown struct
     *
     * @param string $fieldPath
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getCreditDropdownStruct($fieldPath)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'container',
                        'component' => 'Vnecoms_Credit/js/components/dynamic-rows',
                        'template' => 'ui/dynamic-rows/templates/default',
                        'label' => __('Store Credit Value '),
                        'renderDefaultRecord' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => '',
                        'dndConfig' => [
                            'enabled' => false,
                        ],
                        'disabled' => false,
                        'sortOrder' =>
                        $this->arrayManager->get($fieldPath . '/arguments/data/config/sortOrder', $this->meta),
                        'relatedCreditType' => CreditType::TYPE_OPTION,
                        'imports' =>[
                            'handleCreditTypeChange' => '${$.provider}:data.product.credit_type'
                        ],
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => [
                        'duration' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Number::NAME,
                                        'label' => __('Store Credit Value'),
                                        'dataScope' => 'credit_value',
                                        'validation' => [
                                            'validate-zero-or-greater' => true,
                                        ]
                                    ],
                                ],
                            ],
                        ],
                        'price' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Field::NAME,
                                        'formElement' => Input::NAME,
                                        'dataType' => Price::NAME,
                                        'label' => __('Credit Package Price'),
                                        'enableLabel' => true,
                                        'dataScope' => 'credit_price',
                                        'addbefore' => $this->locator->getStore()
                                            ->getBaseCurrency()
                                            ->getCurrencySymbol(),
                                        'validation' => [
                                            'validate-zero-or-greater' => true,
                                        ]
                                    ],
                                ],
                            ],
                        ],
                        'actionDelete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'actionDelete',
                                        'dataType' => Text::NAME,
                                        'label' => '',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Get credit value custom struct
     * 
     * @param string $fieldPath
     * @return array
     */
    protected function getCreditValueCustomStruct($fieldPath)
    {
        $scopeLabel = $this->arrayManager->get($fieldPath . '/arguments/data/config/scopeLabel', $this->meta);
        $sortOrder = $this->arrayManager->get($fieldPath . '/arguments/data/config/sortOrder', $this->meta);
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'breakLine' => false,
                        'label' => __('Store Credit Value'),
                        'required' => false,
                        'additionalClasses' => 'admin__control-grouped-date',
                        'scopeLabel' => $scopeLabel,
                        'sortOrder' => $sortOrder,
                        'dataScope' => 'credit_value_custom',
                        'relatedCreditType' => CreditType::TYPE_RANGE,
                        'component' => 'Vnecoms_Credit/js/components/group',
                        'imports' =>[
                            'handleCreditTypeChange' => '${$.provider}:data.product.credit_type'
                        ],
                    ],
                ],
            ],
            'children' => [
                'credit_value_custom_from' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataType' => Number::NAME,
                                'formElement' => Input::NAME,
                                'visible' => 1,
                                'required' => 0,
                                'notice' => null,
                                'default' => null,
                                'label' => __('Store Credit Value'),
                                'placeholder' => __('From'),
                                'code' => 'credit_value_custom_from',
                                'source' => 'product-details',
                                'scopeLabel' => $scopeLabel,
                                'globalScope' => true,
                                'sortOrder' => 1,
                                'componentType' => Field::NAME,
                                'dataScope' => 'from',
                                'additionalClasses' => 'admin__field-date admin__field-credit-from',
                                'validation' => [
                                    'validate-zero-or-greater' => true,
                                ],
    
                            ],
                        ],
                    ],
                ],
                'credit_value_custom_to' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataType' => Number::NAME,
                                'formElement' => Input::NAME,
                                'visible' => 1,
                                'required' => 0,
                                'notice' => null,
                                'default' => null,
                                /* 'label' => __('To'), */
                                'placeholder' => __('To'),
                                'code' => 'credit_value_custom_to',
                                'source' => 'product-details',
                                'scopeLabel' => $scopeLabel,
                                'globalScope' => true,
                                'sortOrder' => 2,
                                'componentType' => Field::NAME,
                                'dataScope' => 'to',
                                'additionalClasses' => 'admin__field-date admin__field-credit-from',
                                'validation' => [
                                    'validate-zero-or-greater' => true,
                                ],
    
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Get credit type struct
     * 
     * @param string $fieldPath
     * @return array
     */
    protected function getCreditTypeStruct($fieldPath)
    {
        $defaultData = $this->arrayManager->get($fieldPath,$this->meta);
        
        $defaultData['children']['credit_type']['arguments']['data']['config']['valueUpdate'] = 'change';
        return $defaultData;
    }
    
    /**
     * Get credit rate struct
     *
     * @param string $fieldPath
     * @return array
     */
    protected function getCreditRateStruct($fieldPath)
    {
        $defaultData = $this->arrayManager->get($fieldPath,$this->meta);
        
        $defaultData['arguments']['data']['config']['relatedCreditType'] = CreditType::TYPE_RANGE;
        $defaultData['arguments']['data']['config']['component'] = 'Vnecoms_Credit/js/components/group';
        $defaultData['arguments']['data']['config']['imports'] = [
            'handleCreditTypeChange' => '${$.provider}:data.product.credit_type'
        ];
        
        return $defaultData;
    }
    
    
    /**
     * Get credit fixed value struct
     *
     * @param string $fieldPath
     * @return array
     */
    protected function getCreditFixedValueStruct($fieldPath)
    {
        $defaultData = $this->arrayManager->get($fieldPath,$this->meta);
    
        $defaultData['arguments']['data']['config']['relatedCreditType'] = CreditType::TYPE_FIXED;
        $defaultData['arguments']['data']['config']['component'] = 'Vnecoms_Credit/js/components/group';
        $defaultData['arguments']['data']['config']['imports'] = [
            'handleCreditTypeChange' => '${$.provider}:data.product.credit_type'
        ];
    
        return $defaultData;
    }
}

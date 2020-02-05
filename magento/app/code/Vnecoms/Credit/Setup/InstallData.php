<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Credit\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Vnecoms\Credit\Model\Product\Type\Credit;
use \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Customer collection factory
     *
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $_customerCollectionFactory;
 
    /**
     * @var \Vnecoms\Credit\Model\CreditFactory
     */
    private $_creditFactory;
    
    /**
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    private $_categorySetupFactory;
    
    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        CollectionFactory $customerCollectionFactory,
        \Vnecoms\Credit\Model\CreditFactory $creditFactory,
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
    ) {
        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_creditFactory = $creditFactory;
        $this->_categorySetupFactory = $categorySetupFactory;
    }
    
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $categorySetup = $this->_categorySetupFactory->create(
            ['setup' => $setup]
        );
        $setup->startSetup();
        
        $categorySetup->addAttribute(
            Product::ENTITY,
            'credit_type',
            [
                'group' => 'Product Details',
                'label' => 'Type of Store Credit',
                'type' => 'int',
                'input' => 'select',
                'position' => 4,
                'visible' => true,
                'default' => '',
                'visible' => true,
            	'required' => true,
            	'user_defined' => false,
                'source' => 'Vnecoms\Credit\Model\Source\Type',
            	'default' => '',
            	'visible_on_front' => false,
            	'unique' => false,
            	'is_configurable' => false,
            	'used_for_promo_rules' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'apply_to'=>Credit::TYPE_CODE,
            ]
        );
        $categorySetup->addAttribute(
            Product::ENTITY,
            'credit_value_fixed',
            [
                'group' => 'Product Details',
                'label' => 'Store Credit Value',
                'type' => 'varchar',
                'input' => 'text',
                'position' => 4,
                'visible' => true,
                'default' => '',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'apply_to'=>Credit::TYPE_CODE,
            ]
        );
        
        $categorySetup->addAttribute(
            Product::ENTITY,
            'credit_value_dropdown',
            [
                'group' => 'Product Details',
                'label' => 'Store Credit Value',
                'type' => 'varchar',
                'input' => 'text',
                'position' => 4,
                'visible' => true,
                'default' => '',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'backend' => 'Vnecoms\Credit\Model\Product\Attribute\Backend\CreditDropdown',
                'default' => '',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'apply_to'=>Credit::TYPE_CODE,
            ]
        );
        $categorySetup->addAttribute(
            Product::ENTITY,
            'credit_value_custom',
            [
                'group' => 'Product Details',
                'label' => 'Store Credit Value',
                'type' => 'varchar',
                'input' => 'text',
                'position' => 4,
                'visible' => true,
                'default' => '',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'backend' => 'Vnecoms\Credit\Model\Product\Attribute\Backend\CreditCustom',
                'default' => '',
                'note' => 'Enter the range of credit value.',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'apply_to'=>Credit::TYPE_CODE,
            ]
        );
        
        $categorySetup->addAttribute(
            Product::ENTITY,
            'credit_price',
            [
                'group' => 'Product Details',
                'label' => 'Credit Package Price',
                'type' => 'decimal',
                'input' => 'price',
                'position' => 4,
                'visible' => true,
                'default' => '',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'apply_to'=>Credit::TYPE_CODE,
            ]
        );
        
        $categorySetup->addAttribute(
            Product::ENTITY,
            'credit_rate',
            [
                'group' => 'Product Details',
                'label' => 'Credit Rate',
                'type' => 'decimal',
                'input' => 'text',
                'position' => 4,
                'visible' => true,
                'default' => '',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '1',
                'note' => 'For example: 1.5 -> Each $1 you spend you will get 1.5 credit',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'apply_to'=>Credit::TYPE_CODE,
            ]
        );

        /*make sure these attributes are applied for membership product type only*/
        $attributes = [
            'credit_rate',
            'credit_price',
            'credit_value_custom',
            'credit_value_dropdown',
            'credit_value_fixed',
            'credit_type',
        ];
        foreach ($attributes as $attributeCode){
            $categorySetup->updateAttribute(Product::ENTITY, $attributeCode, 'apply_to',Credit::TYPE_CODE);
        }
        
        $attributes = [
            'weight',
            'tax_class_id',
        ];
        foreach ($attributes as $attributeCode) {
            $relatedProductTypes = explode(
                ',',
                $categorySetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode, 'apply_to')
            );
            if (!in_array(Credit::TYPE_CODE, $relatedProductTypes)) {
                $relatedProductTypes[] = Credit::TYPE_CODE;
                $categorySetup->updateAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attributeCode,
                    'apply_to',
                    implode(',', $relatedProductTypes)
                );
            }
        }
        
        
        $customerCollection = $this->_customerCollectionFactory->create();
		$data = [];
		$bunchSize = 1000;
        /*Create credit account for all exist customer*/
		$i = 0;
        foreach($customerCollection as $customer){
			$data[] = ['customer_id'=>$customer->getId(), 'credit'=>0];
			if($i ++ >= $bunchSize){
				$this->insertData($data);
				$data = [];
				$i = 0;
			}
        }
		if(sizeof($data)){
			$this->insertData($data);
		}
		
        $setup->endSetup();
    }
	
	protected function insertData(array $data){
		$creditModel = $this->_creditFactory->create();
		$tableName = $creditModel->getCollection()->getTable('ves_store_credit');
		$creditModel->getResource()->getConnection()->insertMultiple($tableName, $data);
	}
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsProduct\Setup;

use Magento\Catalog\Model\Product;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $categorySetupFactory;
 
    

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(\Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        /** @var CustomerSetup $customerSetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $setup->startSetup();


        $categorySetup->addAttribute(
            Product::ENTITY,
            'vendor_id',
            [
                'group' => 'Product Details',
                'label' => 'Vendor Id',
                'type' => 'static',
                'input' => 'text',
                'position' => 145,
                'visible' => true,
                'default' => '',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => true,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true
            ]
        );
        $categorySetup->addAttribute(
            Product::ENTITY,
            'approval',
            [
                'group' => 'Product Details',
                'label' => 'Approval',
                'type' => 'int',
                'input' => 'select',
                'position' => 160,
                'visible' => true,
                'default' => '',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'source' => 'Vnecoms\VendorsProduct\Model\Source\Approval',
                'default' => '',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'used_in_product_listing' => true
            ]
        );
        
        $categorySetup->updateAttribute(Product::ENTITY, 'approval', 'used_in_product_listing',1);
        
        $setup->endSetup();
    }
}

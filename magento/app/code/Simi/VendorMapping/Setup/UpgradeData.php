<?php

/**
 * Copyright © 2020 Simicart. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simi\VendorMapping\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
       * @param EavSetup $eavSetup
       * @param QuoteSetupFactory $setupFactory
       * @param SalesSetupFactory $salesSetupFactory
       */
      public function __construct(
        EavSetup $eavSetup
    ) {
        $this->eavSetup = $eavSetup;
    }

    

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context){
        if ($context->getVersion() && version_compare($context->getVersion(), '0.1.1', '<')) {
            $attributes = [
                'is_admin_sell' => [
                    // 'group'              => '',
                    'input'              => 'boolean',
                    'type'               => 'int',
                    'label'              => 'Sell by Admin',
                    'visible'            => true,
                    'required'           => false,
                    'user_defined'               => false,
                    'searchable'                 => false,
                    'filterable'                 => true,
                    'comparable'                 => false,
                    'visible_on_front'           => false,
                    'visible_in_advanced_search' => false,
                    'is_html_allowed_on_front'   => false,
                    'used_for_promo_rules'       => true,
                    // 'source'                     => Status::class,
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'default' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES,
                    // 'frontend_class'             => '',
                    'global'                     =>  \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'unique'                     => false,
                    'apply_to'                   => 'simple,grouped,configurable,downloadable,virtual,bundle,aw_giftcard'
                ]
            ];
    
            // $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            foreach ($attributes as $attribute_code => $attributeOptions) {
                $this->eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attribute_code,
                    $attributeOptions
                );
            }
        }
    
        if ($context->getVersion() && version_compare($context->getVersion(), '0.1.3', '<')) {

            $attributes = [
                'website' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' =>  255,
                    'comment' => 'Website Url',
                    'label' => 'Website'
                ],
                'facebook' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' =>  255,
                    'comment' => 'Facebook Url',
                    'label' => 'Facebook'
                ],
                'instagram' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' =>  255,
                    'comment' => 'Instagram Url',
                    'label' => 'Instagram'
                ]
            ];

            // $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            foreach ($attributes as $attribute_code => $attributeOptions) {
                $this->eavSetup->addAttribute(
                    \Vnecoms\Vendors\Model\Vendor::ENTITY,
                    $attribute_code,
                    $attributeOptions
                );
            }
        }
    }
}

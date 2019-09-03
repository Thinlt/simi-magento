<?php

namespace Simi\VendorMapping\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

// use Magento\Catalog\Model\Product;
// use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;
// use Magento\Framework\DB\Ddl\Table;
// use Magento\Catalog\Model\ResourceModel\Eav\Attribute as CatalogEavAttribute;
// use Magento\Framework\DB\Adapter\AdapterInterface;

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
}

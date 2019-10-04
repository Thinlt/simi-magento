<?php

namespace Simi\Simicustomize\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

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
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.1', '<')) {
            $attributes = [
                'try_to_buy' => [
                    // 'group'              => '',
                    'input'              => 'boolean',
                    'type'               => 'int',
                    'label'              => 'Try to buy',
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
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'default' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_NO,
                    // 'frontend_class'             => '',
                    'global'                     =>  \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'unique'                     => false,
                    'apply_to'                   => 'simple,grouped,configurable,downloadable,virtual,bundle,aw_giftcard'
                ],
                'reservable' => [
                    // 'group'              => '',
                    'input'              => 'boolean',
                    'type'               => 'int',
                    'label'              => 'Reservable',
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
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'default' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_NO,
                    // 'frontend_class'             => '',
                    'global'                     =>  \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'unique'                     => false,
                    'apply_to'                   => 'simple,grouped,configurable,downloadable,virtual,bundle,aw_giftcard'
                ],
                'pre_order' => [
                    // 'group'              => '',
                    'input'              => 'boolean',
                    'type'               => 'int',
                    'label'              => 'Pre order',
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
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'default' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_NO,
                    // 'frontend_class'             => '',
                    'global'                     =>  \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'unique'                     => false,
                    'apply_to'                   => 'simple,grouped,configurable,downloadable,virtual,bundle,aw_giftcard'
                ]
            ];
    
            foreach ($attributes as $attribute_code => $attributeOptions) {
                $this->eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attribute_code,
                    $attributeOptions
                );
            }
        }

    }
}

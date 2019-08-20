<?php

namespace Vnecoms\Credit\Setup;

use Magento\Catalog\Model\Product;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
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
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var CustomerSetup $customerSetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $setup->startSetup();
        if ($context->getVersion()
            && version_compare($context->getVersion(), '2.0.2') < 0
        ) {
            $categorySetup->updateAttribute(Product::ENTITY, 'credit_type', 'used_in_product_listing',1);
            $categorySetup->updateAttribute(Product::ENTITY, 'credit_value_fixed', 'used_in_product_listing',1);
            $categorySetup->updateAttribute(Product::ENTITY, 'credit_value_dropdown', 'used_in_product_listing',1);
            $categorySetup->updateAttribute(Product::ENTITY, 'credit_value_custom', 'used_in_product_listing',1);
            $categorySetup->updateAttribute(Product::ENTITY, 'credit_price', 'used_in_product_listing',1);
            $categorySetup->updateAttribute(Product::ENTITY, 'credit_rate', 'used_in_product_listing',1);
        }
        
        $setup->endSetup();
    }
}
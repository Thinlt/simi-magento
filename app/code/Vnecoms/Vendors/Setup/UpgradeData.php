<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Vendors\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;


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
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if ($context->getVersion() && version_compare($context->getVersion(), '2.2.2', '<')) {

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

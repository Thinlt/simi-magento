<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Vnecoms\Vendors\Model\Vendor;
use Vnecoms\Vendors\Setup\VendorSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Vendor setup factory
     *
     * @var VendorSetupFactory
     */
    private $vendorSetupFactory;

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;



    private $attributeSetFactory;
    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        VendorSetupFactory $vendorSetupFactory,
        EavSetupFactory $eavSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
    
        $this->vendorSetupFactory = $vendorSetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $ticketSetup = $this->vendorSetupFactory->create(['setup' => $setup]);
        if ($context->getVersion()
            && version_compare($context->getVersion(), '2.1.2') < 0
        ) {
            $ticketSetup->addAttribute(
                'vendor',
                'flag_notify_email',
                [
                    'label' => 'Flag Notify Email',
                    'type' => 'varchar',
                    'input' => 'text',
                    'required' => true,
                    'sort_order' => 70,
                    'position' => 40,
                    'default'=>0
                ]
            );
        }
        $setup->endSetup();
    }

    /**
     * Create page
     *
     * @return Page
     */
    public function createSpam()
    {
        return $this->spamFactory->create();
    }
}

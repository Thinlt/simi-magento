<?php

namespace Simi\VendorMapping\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 *
 * @package Aheadworks\Giftcard\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $connection = $setup->getConnection();
        if ($context->getVersion() && version_compare($context->getVersion(), '0.1.1', '<')) {
            // Add columns to giftcard table
            $connection->addColumn(
                $setup->getTable('aw_giftcard'),
                'vendor_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Vendor ID of Vnecoms_Vendors module'
                ]
            );
            // Add columns to giftcard pool table
            $connection->addColumn(
                $setup->getTable('aw_giftcard_pool'),
                'vendor_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Vendor ID of Vnecoms_Vendors module'
                ]
            );
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '0.1.2', '<')) {
            $connection->addColumn(
                $setup->getTable('review'),
                'vendor_id', 
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => true],
                    'comment' => 'Vnecoms vendor entity_id',
                ]
            );
        }
    }
}

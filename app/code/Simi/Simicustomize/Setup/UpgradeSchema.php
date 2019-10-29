<?php

namespace Simi\Simicustomize\Setup;

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
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.3', '<')) {
            // Add base_deposit_amount to quote table
            $connection->addColumn(
                $setup->getTable('quote'),
                'base_deposit_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'comment' => 'Pre-order base_deposit_amount'
                ]
            );

            // Add deposit_amount to quote table
            $connection->addColumn(
                $setup->getTable('quote'),
                'deposit_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'comment' => 'Pre-order deposit_amount'
                ]
            );

            // Add deposit_amount to quote_address table
            $connection->addColumn(
                $setup->getTable('quote_address'),
                'deposit_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'comment' => 'Pre-order deposit_amount'
                ]
            );
            // Add deposit_amount to quote_address table
            $connection->addColumn(
                $setup->getTable('quote_address'),
                'base_deposit_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'comment' => 'Pre-order base_deposit_amount'
                ]
            );
        }
    }
}

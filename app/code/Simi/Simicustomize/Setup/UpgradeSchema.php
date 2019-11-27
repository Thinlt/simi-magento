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

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.4', '<')) {
            // Add quote_type to quote table
            $connection->addColumn(
                $setup->getTable('quote'),
                'quote_type',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => '12,4',
                    'nullable' => true,
                    'comment' => 'Type of order or quote service'
                ]
            );
            // Add base_remaining_amount to quote table
            $connection->addColumn(
                $setup->getTable('quote'),
                'base_remaining_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'comment' => 'Pre-order base_remaining_amount'
                ]
            );
            // Add remaining_amount to quote table
            $connection->addColumn(
                $setup->getTable('quote'),
                'remaining_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'comment' => 'Pre-order remaining_amount'
                ]
            );
            // Add base_remaining_amount to quote_address table
            $connection->addColumn(
                $setup->getTable('quote_address'),
                'base_remaining_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'comment' => 'Pre-order base_remaining_amount'
                ]
            );
            // Add remaining_amount to quote_address table
            $connection->addColumn(
                $setup->getTable('quote_address'),
                'remaining_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'comment' => 'Pre-order remaining_amount'
                ]
            );
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5', '<')) {
            // Customize simi home category
            $connection->addColumn(
                $setup->getTable('simiconnector_simicategory'),
                'is_show_name',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'length' => '2',
                    'nullable' => true,
                    'comment' => 'Category name is shown yes/no'
                ]
            );
        }
    }
}

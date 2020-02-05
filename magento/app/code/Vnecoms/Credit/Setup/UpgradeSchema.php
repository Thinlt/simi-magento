<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup, 
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            /*Table: quote*/
            $setup->getConnection() ->addColumn(
                $setup->getTable('quote'),
                'credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'comment' => 'Used credit'
                ]
            );
            $setup->getConnection() ->addColumn(
                $setup->getTable('quote'),
                'base_credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'comment' => 'Used credit'
                ]
            );

            /*Table: quote*/
            $setup->getConnection() ->addColumn(
                $setup->getTable('quote_item'),
                'credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'discount_amount',
                    'comment' => 'Used credit'
                ]
            );
            $setup->getConnection() ->addColumn(
                $setup->getTable('quote_item'),
                'base_credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'base_discount_amount',
                    'comment' => 'Used credit'
                ]
            );
            /*Table: quote_address*/
            $setup->getConnection() ->addColumn(
                $setup->getTable('quote_address'),
                'credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'base_discount_amount',
                    'comment' => 'Credit Amount'
                ]
            );
            $setup->getConnection() ->addColumn(
                $setup->getTable('quote_address'),
                'base_credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'credit_amount',
                    'comment' => 'Base Credit Amount'
                ]
            );

            /*Table: sales_order*/
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_order'),
                'base_credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'base_discount_amount',
                    'comment' => 'Used credit'
                ]
            );
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_order'),
                'credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'discount_amount',
                    'comment' => 'Used credit'
                ]
            );

            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_order'),
                'base_credit_refunded',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'base_discount_refunded',
                    'comment' => ' Refunded to Store Credit '
                ]
            );
            
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_order'),
                'credit_refunded',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'discount_refunded',
                    'comment' => ' Refunded to Store Credit '
                ]
            );
            
            /*Table: quote*/
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_order_item'),
                'credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'discount_amount',
                    'comment' => 'Used credit'
                ]
            );
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_order_item'),
                'base_credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'base_discount_amount',
                    'comment' => 'Used credit'
                ]
            );
            
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_order_item'),
                'credit_invoiced',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'discount_invoiced',
                    'comment' => 'Used credit'
                ]
            );
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_order_item'),
                'base_credit_invoiced',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'credit_invoiced',
                    'comment' => 'Used credit'
                ]
            );
            
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_order_item'),
                'credit_refunded',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'discount_refunded',
                    'comment' => 'Used credit'
                ]
            );
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_order_item'),
                'base_credit_refunded',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'base_discount_refunded',
                    'comment' => 'Used credit'
                ]
            );
            
            /*Table: sales_invoice*/
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_invoice'),
                'base_credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'base_discount_amount',
                    'comment' => 'Used credit'
                ]
            );
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_invoice'),
                'credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'discount_amount',
                    'comment' => 'Used credit'
                ]
            );

            /*Table: quote*/
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_invoice_item'),
                'credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'discount_amount',
                    'comment' => 'Used credit'
                ]
            );
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_invoice_item'),
                'base_credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'base_discount_amount',
                    'comment' => 'Used credit'
                ]
            );
            /*Table: sales_credit_memo*/
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_creditmemo'),
                'base_credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'base_discount_amount',
                    'comment' => 'Used credit'
                    
                ]
            );
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_creditmemo'),
                'credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'discount_amount',
                    'comment' => 'Used credit'
                ]
            );
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_creditmemo'),
                'base_credit_refunded',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'comment' => ' Refunded to Store Credit '
                ]
            );
            
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_creditmemo'),
                'credit_refunded',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'base_credit_refunded',
                    'comment' => ' Refunded to Store Credit '
                ]
            );
            
            /*Table: sales_creditmemo_item*/
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_creditmemo_item'),
                'credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'discount_amount',
                    'comment' => 'Used credit'
                ]
            );
            $setup->getConnection() ->addColumn(
                $setup->getTable('sales_creditmemo_item'),
                'base_credit_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'after' => 'base_discount_amount',
                    'comment' => 'Used credit'
                ]
            );
            
            
        }
        $setup->endSetup();
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsSales\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('quote_item'),
                'vendor_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'after' => 'quote_id',
                    'comment' => 'Vendor Id'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_item'),
                'vendor_order_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'after' => 'order_id',
                    'comment' => 'Vendor Order Id'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_item'),
                'vendor_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'after' => 'order_id',
                    'comment' => 'Vendor Id'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_invoice_item'),
                'vendor_invoice_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'after' => 'entity_id',
                    'comment' => 'Vendor Id'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_shipment'),
                'vendor_order_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'after' => 'entity_id',
                    'comment' => 'Vendor Id'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_shipment_grid'),
                'vendor_order_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'after' => 'entity_id',
                    'comment' => 'Vendor Id'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_creditmemo'),
                'vendor_order_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'after' => 'entity_id',
                    'comment' => 'Vendor Id'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_creditmemo_grid'),
                'vendor_order_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'after' => 'entity_id',
                    'comment' => 'Vendor Id'
                ]
            );
        }
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            /*Change the created at column*/
            $setup->getConnection()->changeColumn(
                $setup->getTable('ves_vendor_sales_order'),
                'created_at',
                'created_at',
				[
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
					'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                    'comment' => 'Created At'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('ves_vendor_sales_order'),
                'total_qty_ordered',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'after' => 'total_paid',
                    'comment' => 'Total Qty Ordered'
                ]
            );
        }
        
        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_status_history'),
                'vendor_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'after' => 'parent_id',
                    'comment' => 'Vendor Id'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_status_history'),
                'vendor_order_status',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'after' => 'status',
                    'comment' => 'Vendor Statuc Order'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_status_history'),
                'vendor_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'after' => 'parent_id',
                    'comment' => 'Vendor Id'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_status_history'),
                'vendor_order_status',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'after' => 'status',
                    'comment' => 'Vendor Statuc Order'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ves_vendor_sales_order'),
                'base_tax_canceled',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => "12,4",
                    'nullable' => true,
                    'after' => 'total_refunded',
                    'comment' => 'Base Tax Canceled'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('ves_vendor_sales_order'),
                'tax_canceled',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => "12,4",
                    'nullable' => true,
                    'after' => 'total_refunded',
                    'comment' => 'Tax Canceled'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('ves_vendor_sales_order'),
                'base_tax_invoiced',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => "12,4",
                    'nullable' => true,
                    'after' => 'total_refunded',
                    'comment' => 'Base Tax Invoiced'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('ves_vendor_sales_order'),
                'tax_invoiced',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => "12,4",
                    'nullable' => true,
                    'after' => 'total_refunded',
                    'comment' => 'Tax Invoiced'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('ves_vendor_sales_order'),
                'base_tax_refunded',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => "12,4",
                    'nullable' => true,
                    'after' => 'total_refunded',
                    'comment' => 'Base Tax Refunded'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('ves_vendor_sales_order'),
                'tax_refunded',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => "12,4",
                    'nullable' => true,
                    'after' => 'total_refunded',
                    'comment' => 'Tax Refunded'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('ves_vendor_sales_order'),
                'base_subtotal_incl_tax',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => "12,4",
                    'nullable' => true,
                    'after' => 'subtotal_incl_tax',
                    'comment' => 'Base Subtotal Incl Tax'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.4', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ves_vendor_sales_order'),
                'shipping_tax_refunded',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => "12,4",
                    'nullable' => true,
                    'after' => 'shipping_tax_amount',
                    'comment' => 'Shipping Tax Refunded'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('ves_vendor_sales_order'),
                'base_shipping_tax_refunded',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => "12,4",
                    'nullable' => true,
                    'after' => 'base_shipping_tax_amount',
                    'comment' => 'Base Shipping Tax Refunded'
                ]
            );
        }
        
        $setup->endSetup();
    }
}

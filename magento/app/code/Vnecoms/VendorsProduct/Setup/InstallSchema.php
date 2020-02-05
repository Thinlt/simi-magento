<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsProduct\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'customer_address_entity'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_vendor_product_attribute_set')
        )->addColumn(
            'attribute_set_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'parent_set_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['nullable' => false,'unsigned' => true],
            'Parent Attribute Set Id'
        )->addColumn(
            'display_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Display groups type'
        )->addColumn(
            'attribute_set_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Attribute Set Name'
        )
        ->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Sort Order'
        )->addForeignKey(
            $installer->getFkName('ves_vendor_product_attribute_set', 'parent_set_id', 'eav_attribute_set', 'attribute_set_id'),
            'parent_set_id',
            $installer->getTable('eav_attribute_set'),
            'attribute_set_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Product Attribute Set'
        );
        $installer->getConnection()->createTable($table);
        
        /**
         * Create table 'ves_vendor_entity_datetime'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_vendor_product_attribute_group')
        )->addColumn(
            'group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true,'unsigned' => true, 'nullable' => false, 'primary' => true],
            'group Id'
        )->addColumn(
            'attribute_set_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Attribute Set Id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Name'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Sort Order'
        )->addForeignKey(
            $installer->getFkName('ves_vendor_product_attribute_group', 'attribute_set_id', 'ves_vendor_product_attribute_set', 'attribute_set_id'),
            'attribute_set_id',
            $installer->getTable('ves_vendor_product_attribute_set'),
            'attribute_set_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Attribute group'
        );
        $installer->getConnection()->createTable($table);
        
        /**
         * Create table 'customer_entity_decimal'
        */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_vendor_product_entity_attribute')
        )->addColumn(
            'entity_attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Value Id'
        )->addColumn(
            'attribute_set_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Attribute Set Id'
        )->addColumn(
            'attribute_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Entity Id'
        )->addColumn(
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Entity Id'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Sort Order'
        )->addForeignKey(
            $installer->getFkName('ves_vendor_product_entity_attribute', 'attribute_set_id', 'ves_vendor_product_attribute_set', 'attribute_set_id'),
            'attribute_set_id',
            $installer->getTable('ves_vendor_product_attribute_set'),
            'attribute_set_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('ves_vendor_product_entity_attribute', 'attribute_group_id', 'ves_vendor_product_attribute_group', 'group_id'),
            'attribute_group_id',
            $installer->getTable('ves_vendor_product_attribute_group'),
            'group_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('ves_vendor_product_entity_attribute', 'attribute_id', 'eav_attribute', 'attribute_id'),
            'attribute_id',
            $installer->getTable('eav_attribute'),
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Vendor Entity Decimal'
        );
        $installer->getConnection()->createTable($table);
        
        
        $installer->endSetup();
    }
}

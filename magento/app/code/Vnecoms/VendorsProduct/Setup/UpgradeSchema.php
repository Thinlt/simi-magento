<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsProduct\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Media;
use Magento\Catalog\Model\Product\Attribute\Backend\Media\ImageEntryConverter;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $table = $setup->getConnection()->addColumn(
				$setup->getTable('catalog_product_entity'),
				'vendor_id',
				[
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'size' => 10,
                    'nullable' => false,
					'unsigned' => true,
					'default' => 0,
					'after' => 'type_id',
                    'comment' => 'Vendor Id'
                ]
			);
        }
        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            /**
             * Create table 'ves_vendor_product_update'
             */
            $table = $setup->getConnection()->newTable(
                $setup->getTable('ves_vendor_product_update')
            )->addColumn(
                'update_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'vendor_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => false,],
                'Vendor Id'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'unsigned' => true, 'nullable' => false,],
                'Store Id'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product Id'
            )->addColumn(
                'product_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
                ['nullable' => false],
                'Changed Datas'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned' => true, 'nullable' => false,],
                'Status'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addForeignKey(
                $setup->getFkName('ves_vendor_product_update', 'vendor_id', 'ves_vendor_entity', 'entity_id'),
                'vendor_id',
                $setup->getTable('ves_vendor_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Vendor Product Update'
            );
            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}

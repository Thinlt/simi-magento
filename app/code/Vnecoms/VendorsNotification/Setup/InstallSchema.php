<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsNotification\Setup;

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
         * Create table 'ves_vendor_notification'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('ves_vendor_notification')
        )->addColumn(
            'notification_id',
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
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [ 'unsigned' => true, 'nullable' => false,],
            'Notification Type'
        )->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false],
            'Title'
        )->addColumn(
            'additional_info',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            128,
            ['nullable' => false],
            'Additional info to build the URL'
        )->addColumn(
            'is_read',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            [ 'unsigned' => true, 'nullable' => false, 'default' => 0],
            'Is Read'
        )->addColumn(
            'is_reached',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            [ 'unsigned' => true, 'nullable' => false,'default' => 0],
            'Is reached'
        )->addColumn(
            'is_hidden',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            [ 'unsigned' => true, 'nullable' => false,'default' => 0],
            'Is reached'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addForeignKey(
            $setup->getFkName('ves_vendor_notification', 'vendor_id', 'ves_vendor_entity', 'entity_id'),
            'vendor_id',
            $setup->getTable('ves_vendor_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Vendor notificaiton'
        );
        $setup->getConnection()->createTable($table);
        
        
        $installer->endSetup();
    }
}

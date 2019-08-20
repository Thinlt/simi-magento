<?php
namespace Vnecoms\VendorsCustomWithdrawal\Setup;

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
         * Create table 'ves_vendor_withdrawal_method'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_vendor_withdrawal_method')
        )->addColumn(
            'method_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Method Id'
        )->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Method Code'
        )->addColumn(
            'is_enabled',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            [],
            'Is Enabled'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            64,
            [],
            'Method Title'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Method Description'
        )->addColumn(
            'fee_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            8,
            [],
            'Fee Type'
        )->addColumn(
            'fee',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12,4],
            [],
            'Method Fee'
        )->addColumn(
            'min_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12,4],
            [],
            'Min Amount'
        )->addColumn(
            'max_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12,4],
            [],
            'Max Amount'
        )->addColumn(
            'fields',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['nullable' => true],
            'Method Data'
        )->setComment(
            'Vendor Withdrawal Methods'
        );
        $installer->getConnection()->createTable($table);
        
        /**
         * Create table 'ves_vendor_withdrawal_method_data'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_vendor_withdrawal_method_data')
        )->addColumn(
            'data_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Data Id'
        )->addColumn(
            'method_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Method Id'
        )->addColumn(
            'vendor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Vendor Id'
        )->addColumn(
            'method_data',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
            [],
            'Method Data'
        )->addForeignKey(
            $installer->getFkName('ves_vendor_withdrawal_method_data', 'vendor_id', 'ves_vendor_entity', 'entity_id'),
            'vendor_id',
            $installer->getTable('ves_vendor_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('ves_vendor_withdrawal_method_data', 'method_id', 'ves_vendor_withdrawal_method', 'method_id'),
            'method_id',
            $installer->getTable('ves_vendor_withdrawal_method'),
            'method_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Withdrawal Method Data'
        );
        $installer->getConnection()->createTable($table);
    }
}

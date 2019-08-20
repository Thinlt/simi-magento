<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfProCustomVariables\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /*
         * Create table 'ves_pdfprocustomvariables_customvariables'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_pdfprocustomvariables_customvariables')
        )->addColumn(
            'custom_variable_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Custom Variable ID'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            31,
            ['nullable' => false, 'default' => ''],
            'Custom Variable Name'
        )->addColumn(
            'variable_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            31,
            ['nullable' => false],
            'Variable Type'
        )->addColumn(
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'unsigned' => true],
            'Attribute ID'
        )->addColumn(
            'static_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => true],
            'Static Value'
        )->addIndex(
            $installer->getIdxName('ves_pdfprocustomvariables_customvariables', ['name']),
            ['name']
        )->setComment(
            'Custom Variables Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}

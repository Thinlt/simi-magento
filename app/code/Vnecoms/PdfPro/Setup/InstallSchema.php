<?php

namespace Vnecoms\PdfPro\Setup;

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
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /*
         * Drop tables if exists
         */
        $installer->getConnection()->dropTable($installer->getTable('ves_pdfpro_key'));

        /*
         * Create table ves_pdfpro_key
         */
        if (!$installer->tableExists('ves_pdfpro_key')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('ves_pdfpro_key')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->addColumn(
                'api_key',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'api_key'
            )->addColumn(
                'store_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'store_ids'
            )->addColumn(
                'customer_group_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'customer_group_ids'
            )->addColumn(
                'priority',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => '0'],
                'priority'
            )->addColumn(
                'comment',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'comment'
            )->addColumn(
                'is_default',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => '0'],
                'is_default'
            )->addColumn(
                'css',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                [
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'css',
                ]
            )->addColumn(
                'order_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                [
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'order_template',
                ]
            )->addColumn(
                'invoice_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                [
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'invoice_template',
                ]
            )->addColumn(
                'shipment_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                [
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'shipment_template',
                ]
            )->addColumn(
                'creditmemo_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                [
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'creditmemo_template',
                ]
            )->addColumn(
                'template_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                '6',
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'template_id',
                ]
            )->addColumn(
                'custom1_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                [
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'custom1_template',
                ]
            )->addColumn(
                'custom2_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                [
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'custom2_template',
                ]
            )->addColumn(
                'custom3_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                [
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'custom3_template',
                ]
            )->addColumn(
                'rtl',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                6,
                [
                    'nullable' => true,
                    'comment' => 'rtl',
                ]
            )->addColumn(
                'water_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                [
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'water_image',
                ]
            )->addColumn(
                'water_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                [
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'water_text',
                ]
            )->addColumn(
                'water_alpha',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                [
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'water_alpha',
                ]
            );
            $installer->getConnection()->createTable($table);
            /*
             * End create table ves_pdfpro_key
             */
        }

        /*
         * Create table ves_pdfpro_template
         */
        if (!$installer->tableExists('ves_pdfpro_template')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('ves_pdfpro_template')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'name'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'sku'
            )->addColumn(
                'css_path',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'css_path'
            )->addColumn(
                'order_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                ['nullable' => false, 'default' => ''],
                'order_template'
            )->addColumn(
                'invoice_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                ['nullable' => false, 'default' => ''],
                'invoice_template'
            )->addColumn(
                'shipment_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                ['nullable' => false, 'default' => ''],
                'shipment_template'
            )->addColumn(
                'creditmemo_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                ['nullable' => false, 'default' => ''],
                'creditmemo_template'
            )->addColumn(
                'preview_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                [
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'preview image',
                ]
            )
            ;
            $installer->getConnection()->createTable($table);
            /*
             * End create table ves_pdfpro_template
             */
        }

        /*
         * Create table ves_pdfpro_variable
         */
        if (!$installer->tableExists('ves_pdfpro_variable')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('ves_pdfpro_variable')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'entity_id'
            )->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'category_id'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => ''],
                'title'
            )->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                ['nullable' => false, 'default' => ''],
                'code'
            )->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false, 'default' => '0'],
                'sort_order'
            )
            ;
            $installer->getConnection()->createTable($table);
            /*
             * End create table ves_pdfpro_variable
             */
        }

        /*
         * Create table ves_pdfpro_category
         */
        if (!$installer->tableExists('ves_pdfpro_category')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('ves_pdfpro_category')
            )->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'category_id'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'title'
            )->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'code'
            )->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false, 'default' => '0'],
                'sort_order'
            )->addColumn(
                'type_variable',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false, 'default' => '1'],
                'type_variable'
            )
            ;
            $installer->getConnection()->createTable($table);
            /*
             * End create table ves_pdfpro_category
             */
        }

        $installer->endSetup();
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Core\Setup;

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
         * Create table 'ves_core_licensekey'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_core_licensekey')
        )->addColumn(
            'key_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Key ID'
        )->addColumn(
            'license_key',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'License Key'
        )->addColumn(
            'license_info',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
            ['nullable' => false],
            'License Info'
        )->addColumn(
            'additional_nfo',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
            ['nullable' => true],
            'Description'
        );
        
        $installer->getConnection()->createTable($table);
                
        $installer->endSetup();

    }
}

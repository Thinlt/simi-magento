<?php

namespace Vnecoms\PdfPro\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.1.0.1') < 0) {
            $connection = $installer->getConnection();

            $installer->getConnection()->addColumn(
                $installer->getTable('ves_pdfpro_key'),
                'logo',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'logo',
                ]
            );
        }
        $installer->endSetup();
    }
}

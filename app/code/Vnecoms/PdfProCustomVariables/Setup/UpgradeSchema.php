<?php

namespace Vnecoms\PdfProCustomVariables\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    public function upgrade(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.0.1') < 0) {
            $table = $setup->getConnection()->addColumn($setup->getTable(
                'ves_pdfprocustomvariables_customvariables'),
                'attribute_id_customer',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'unsigned' => true,
                    'after' => 'attribute_id',
                    'comment' => 'Attribute ID Customer'
                ]
            );
        }

        $setup->endSetup();
    }
}

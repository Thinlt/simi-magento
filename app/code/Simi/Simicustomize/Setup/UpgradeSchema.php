<?php

namespace Simi\Simicustomize\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 *
 * @package Aheadworks\Giftcard\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $connection = $setup->getConnection();
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.3', '<')) {

        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.4', '<')) {

        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5', '<')) {
            // Customize simi home category
            $connection->addColumn(
                $setup->getTable('simiconnector_simicategory'),
                'is_show_name',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'length' => '2',
                    'nullable' => true,
                    'comment' => 'Category name is shown yes/no'
                ]
            );
        }
    }
}

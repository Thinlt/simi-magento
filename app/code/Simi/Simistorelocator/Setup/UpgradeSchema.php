<?php

namespace Simi\Simistorelocator\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Simi\Simistorelocator\Setup\InstallSchema as StorelocatorShema;

class UpgradeSchema implements UpgradeSchemaInterface {

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->changeColumnImage($setup);
        }

        $installer->endSetup();
    }

    /**
     *
     * rename column simistorelocator_id in table simi_simistorelocator_image to locator_id
     *
     * @param SchemaSetupInterface $setup
     */
    public function changeColumnImage(SchemaSetupInterface $setup) {
        $setup->getConnection()->dropForeignKey(
                $setup->getTable(StorelocatorShema::SCHEMA_IMAGE), $setup->getFkName(
                        StorelocatorShema::SCHEMA_IMAGE, 'simistorelocator_id', StorelocatorShema::SCHEMA_STORE, 'simistorelocator_id'
                )
        );

        $setup->getConnection()->dropIndex(
                $setup->getTable(StorelocatorShema::SCHEMA_IMAGE), $setup->getIdxName(
                        $setup->getTable(StorelocatorShema::SCHEMA_IMAGE), ['simistorelocator_id'], AdapterInterface::INDEX_TYPE_INDEX
                )
        );

        $setup->getConnection()->changeColumn(
                $setup->getTable(StorelocatorShema::SCHEMA_IMAGE), 'simistorelocator_id', 'locator_id', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length' => null,
            'comment' => 'Storelocator Id',
            'unsigned' => true
                ]
        );

        $setup->getConnection()->addIndex(
                $setup->getTable(StorelocatorShema::SCHEMA_IMAGE), $setup->getIdxName(
                        $setup->getTable(StorelocatorShema::SCHEMA_IMAGE), ['locator_id'], AdapterInterface::INDEX_TYPE_INDEX
                ), ['locator_id'], AdapterInterface::INDEX_TYPE_INDEX
        );

        $setup->getConnection()->addForeignKey(
                $setup->getFkName(
                        StorelocatorShema::SCHEMA_IMAGE, 'locator_id', StorelocatorShema::SCHEMA_STORE, 'simistorelocator_id'
                ), $setup->getTable(StorelocatorShema::SCHEMA_IMAGE), 'locator_id', $setup->getTable(StorelocatorShema::SCHEMA_STORE), 'simistorelocator_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
    }

}

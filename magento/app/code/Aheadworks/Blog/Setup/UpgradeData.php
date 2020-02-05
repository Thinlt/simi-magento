<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Blog\Setup;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Serialize\Factory as SerializeFactory;
use Aheadworks\Blog\Model\Serialize\SerializeInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Aheadworks\Blog\Model\Source\Post\CustomerGroups;
use Aheadworks\Blog\Model\ResourceModel\Post as ResourcePost;

/**
 * Class UpgradeData
 * @package Aheadworks\Blog\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var SerializeInterface
     */
    private $serializer;

    /**
     * @param SerializeFactory $serializeFactory
     */
    public function __construct(
        SerializeFactory $serializeFactory
    ) {
        $this->serializer = $serializeFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if ($context->getVersion() && version_compare($context->getVersion(), '2.4.0', '<')) {
            $this->updateCustomerGroupsForPosts($setup);
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '2.4.6', '<')) {
            $this->convertSerializedConditionsToJson($setup);
        }
        $setup->endSetup();
    }

    /**
     * Fill up all 'customer_groups' fields with 'all groups' value
     *
     * @param ModuleDataSetupInterface $setup
     */
    private function updateCustomerGroupsForPosts(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $connection->update(
            $setup->getTable(ResourcePost::BLOG_POST_TABLE),
            [
                'customer_groups' => CustomerGroups::ALL_GROUPS
            ]
        );
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function convertSerializedConditionsToJson($setup)
    {
        $connection = $setup->getConnection();
        $table = $setup->getTable('aw_blog_post');
        $select = $connection->select()->from(
            $table,
            [
                PostInterface::ID,
                PostInterface::PRODUCT_CONDITION
            ]
        );
        $rulesConditions = $connection->fetchAssoc($select);
        foreach ($rulesConditions as $ruleConditions) {
            $unserializeCond = $this->unserialize($ruleConditions[PostInterface::PRODUCT_CONDITION]);
            if ($unserializeCond !== false) {
                $ruleConditions[PostInterface::PRODUCT_CONDITION] = empty($unserializeCond)
                    ? ''
                    : $this->serializer->serialize($unserializeCond);

                $connection->update(
                    $table,
                    [
                        PostInterface::PRODUCT_CONDITION => $ruleConditions[PostInterface::PRODUCT_CONDITION]
                    ],
                    PostInterface::ID . ' = ' . $ruleConditions[PostInterface::ID]
                );
            }
        }
    }

    /**
     * Unserialize string with unserialize method
     *
     * @param $string
     * @return array|bool
     */
    private function unserialize($string)
    {
        $result = '';
        if (!empty($string)) {
            $result = @unserialize($string);
            if ($result !== false || $string === 'b:0;') {
            } else {
                $result = false;
            }
        }
        return $result;
    }
}

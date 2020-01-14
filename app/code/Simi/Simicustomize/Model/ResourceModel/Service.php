<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\Simicustomize\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
// use Magento\Framework\Model\ResourceModel\Db\Context as DatabaseContext;
// use Simi\Simicustomize\Model\ServiceFactory;

/**
 * Class Giftcard
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel
 */
class Service extends AbstractDb
{
    /**
     * @var ServiceFactory
     */
    // protected $serviceFactory;

    /**
     * @param DatabaseContext $context
     * @param ServiceFactory $metaFactory
     * @param string $connectionName
     */
    // public function __construct(
    //     DatabaseContext $context,
    //     ServiceFactory $serviceFactory,
    //     $connectionName = null
    // ) {
    //     $this->serviceFactory = $serviceFactory;
    //     parent::__construct($context, $connectionName);
    // }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('simi_service', 'id');
    }

    /**
     * Retrieves increment_id
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getNextIncrementId()
    {
        // $service = $this->serviceFactory->create();
        $connection = $this->getConnection();
        $bind = ['increment_id' => 'increment_id'];
        $select = $connection->select()->from(
            $this->getMainTable(),
            ['increment_id']
        )->where('1')->order('increment_id desc')->limit(1);
        $incrementId = $connection->fetchOne($select, $bind);
        if (!$incrementId) {
            $incrementId = '000000000';
        }
        $incrementId = sprintf('%09d', (int)$incrementId + 1);
        return $incrementId;
    }
}

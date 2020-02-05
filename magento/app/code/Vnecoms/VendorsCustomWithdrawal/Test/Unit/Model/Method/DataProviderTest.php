<?php
/**
 * Copyright © Vnecoms, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsCustomWithdrawal\Test\Unit\Model\Method;

use Magento\Framework\App\Request\DataPersistorInterface;
use Vnecoms\VendorsCustomWithdrawal\Model\Method\DataProvider as WithdrawalDataProvider;
use Vnecoms\VendorsCustomWithdrawal\Model\ResourceModel\Method\CollectionFactory;

class DataProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var WithdrawalDataProvider
     */
    protected $model;

    /**
     * @var CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var \Vnecoms\VendorsCustomWithdrawal\Model\ResourceModel\Method\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionMock;

    /**
     * @var DataPersistorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataPersistorMock;

    protected function setUp()
    {

        $this->collectionFactoryMock = $this->createPartialMock(
            \Vnecoms\VendorsCustomWithdrawal\Model\ResourceModel\Method\CollectionFactory::class,
            ['create']
        );
        $this->collectionMock = $this->createMock(\Vnecoms\VendorsCustomWithdrawal\Model\ResourceModel\Method\Collection::class);
        $this->collectionFactoryMock->expects($this->once())->method('create')->willReturn($this->collectionMock);
        $this->dataPersistorMock = $this->createMock(DataPersistorInterface::class);

        $this->model = new \Vnecoms\VendorsCustomWithdrawal\Model\Method\DataProvider(
            'Name',
            'Primary',
            'Request',
            $this->collectionFactoryMock,
            $this->dataPersistorMock
        );
    }

    public function testGetData()
    {
        $methodId = 2;
        $methodData = [
            'method_id' => 2,
            'code' => 'sample_method',
            'is_enabled' => '1',
            'title' => 'Method Dummy Title',
            'description' => 'Method Dummy Title',
            'fee_type' => 'fixed',
            'fee' => 7.9000,
            'min_value' => 7.9000,
            'max_value' => 50.0000,
            'fields' => '[{"label":"test1","input_type":"text","frontend_class":"Test1","position":"1"},{"label":"test2","input_type":"text","frontend_class":"Test2","position":"2"}]',
            'method_fields' => [

            ]
        ];

        $expectedResult = ['key' => 'value'];

        $methodMock = $this->createMock(\Vnecoms\VendorsCustomWithdrawal\Model\Method::class);
        $this->collectionMock->expects($this->once())->method('getItems')->willReturn([$methodMock]);

        $result = $this->model->getData();
        $this->assertNotNull($result);
    }
}

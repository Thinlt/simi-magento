<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Model\Adapter\Mysql\Dynamic;

use Magento\Catalog\Model\Layer\Filter\Price\Range;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Indexer\DimensionFactory;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface as MysqlDataProviderInterface;
use Magento\Framework\Search\Dynamic\IntervalFactory;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManager;
use \Magento\Framework\Search\Request\IndexScopeResolverInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends \Magento\CatalogSearch\Model\Adapter\Mysql\Dynamic\DataProvider
{
	/**
     * @var Resource
     */
    private $resource;

    /**
     * @var Range
     */
    private $range;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var MysqlDataProviderInterface
     */
    private $dataProvider;

    /**
     * @var IntervalFactory
     */
    private $intervalFactory;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var IndexScopeResolverInterface
     */
    private $priceTableResolver;

    /**
     * @var DimensionFactory|null
     */
    private $dimensionFactory;
		
	/**
     * @param ResourceConnection $resource
     * @param Range $range
     * @param Session $customerSession
     * @param MysqlDataProviderInterface $dataProvider
     * @param IntervalFactory $intervalFactory
     * @param StoreManager $storeManager
     * @param IndexScopeResolverInterface|null $priceTableResolver
     * @param DimensionFactory|null $dimensionFactory
     */
    public function __construct(
        ResourceConnection $resource,
        Range $range,
        Session $customerSession,
        MysqlDataProviderInterface $dataProvider,
        IntervalFactory $intervalFactory,
        StoreManager $storeManager = null,
        IndexScopeResolverInterface $priceTableResolver = null,
        DimensionFactory $dimensionFactory = null
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->range = $range;
        $this->customerSession = $customerSession;
        $this->dataProvider = $dataProvider;
        $this->intervalFactory = $intervalFactory;
        $this->storeManager = $storeManager ?: ObjectManager::getInstance()->get(StoreManager::class);
        $this->priceTableResolver = $priceTableResolver ?: ObjectManager::getInstance()->get(
            IndexScopeResolverInterface::class
        );
        $this->dimensionFactory = $dimensionFactory ?: ObjectManager::getInstance()->get(DimensionFactory::class);
				parent::__construct($resource, $range, $customerSession, $dataProvider, $intervalFactory, $storeManager, $priceTableResolver, $dimensionFactory);
    }
		
    /**
     * {@inheritdoc}
     */
    public function getAggregation(
        BucketInterface $bucket,
        array $dimensions,
        $range,
        \Magento\Framework\Search\Dynamic\EntityStorage $entityStorage
    ) {
        $select = $this->dataProvider->getDataSet($bucket, $dimensions, $entityStorage->getSource());
        $column = $select->getPart(Select::COLUMNS)[0];
        $select->reset(Select::COLUMNS);
        $rangeExpr = new \Zend_Db_Expr(
            $this->connection->getIfNullSql(
                $this->connection->quoteInto('FLOOR(main_table.' . $column[1] . ' / ? ) + 1', $range),
                1
            )
        );

        $select
            ->columns(['range' => $rangeExpr])
            ->columns(['metrix' => 'COUNT(*)'])
            ->group('range')
            ->order('range');
        $result = $this->connection->fetchPairs($select);

        return $result;
    }
}

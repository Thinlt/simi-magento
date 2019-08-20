<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Model\Adapter\Mysql\Aggregation;

use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Model\Stock;
use Magento\Customer\Model\Session;
use Magento\Eav\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\App\ObjectManager;
use Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation\DataProvider\SelectBuilderForAttribute;

class DataProvider implements DataProviderInterface
{
    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Vnecoms\VendorsProduct\Helper\Data
     */
    protected $productHelper;
    
    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var SelectBuilderForAttribute
     */
    private $selectBuilderForAttribute;
    
    /**
     * @param \Vnecoms\Vendors\Helper\Data $helper
     * @param \Vnecoms\VendorsProduct\Helper\Data $productHelper
     * @param Config $eavConfig
     * @param ResourceConnection $resource
     * @param ScopeResolverInterface $scopeResolver
     * @param SelectBuilderForAttribute $selectBuilderForAttribute
     */
    public function __construct(
        \Vnecoms\Vendors\Helper\Data $helper,
        \Vnecoms\VendorsProduct\Helper\Data $productHelper,
        Config $eavConfig,
        ResourceConnection $resource,
        ScopeResolverInterface $scopeResolver,
        SelectBuilderForAttribute $selectBuilderForAttribute = null
    ) {
        $this->helper = $helper;
        $this->productHelper = $productHelper;
        $this->eavConfig = $eavConfig;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->scopeResolver = $scopeResolver;
        $this->selectBuilderForAttribute = $selectBuilderForAttribute
        ?: ObjectManager::getInstance()->get(SelectBuilderForAttribute::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSet(
        BucketInterface $bucket,
        array $dimensions,
        Table $entityIdsTable
    ) {

        $currentScope = $this->scopeResolver->getScope($dimensions['scope']->getValue())->getId();
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $bucket->getField());
        $select = $this->getSelect();
        
        $select->joinInner(
            ['entities' => $entityIdsTable->getName()],
            'main_table.entity_id  = entities.entity_id',
            []
        );
        $select = $this->selectBuilderForAttribute->build($select, $attribute, $currentScope);

        $approvalValue = '('.implode(',',$this->getAllowedApprovalStatus()).')';
        $select->join(
                ['at_approval'=>$this->resource->getTableName('catalog_product_entity_int')],
                "at_approval.entity_id = main_table.entity_id AND at_approval.attribute_id = '".$this->getIdOfAttributeCode('catalog_product','approval')."'"
                ." AND at_approval.value IN ".$approvalValue." AND at_approval.store_id = '0'", //@todo dont know why need to 0
                []
        );

        $vendorIds = $this->getNotAllowVendorIds();
        if (sizeof($vendorIds)) {
            $select->join(
                ['product_entity'=>$this->resource->getTableName('catalog_product_entity')],
                "product_entity.entity_id = main_table.entity_id AND product_entity.vendor_id NOT IN (".implode(',', $vendorIds).")",
                []
            );
        }
        
        return $select;

    }

    /**
     * {@inheritdoc}
     */
    public function execute(Select $select)
    {
        return $this->connection->fetchAssoc($select);
    }

    /**
     * @return Select
     */
    private function getSelect()
    {
        return $this->connection->select();
    }

    public function getIdOfAttributeCode($entityCode, $code)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Eav\Model\ResourceModel\Entity\Attribute')
            ->getIdByCode($entityCode,$code);
    }

    /**
     * @return int[]
     */
    protected function getAllowedApprovalStatus()
    {
        return $this->productHelper->getAllowedApprovalStatus();
    } 

    /**
     * @return int[]
     */
    protected function getNotAllowVendorIds()
    {
        return $this->helper->getNotActiveVendorIds();
    } 
}

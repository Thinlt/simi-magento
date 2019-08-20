<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Model\Plugin\Aggregation\Category;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\Search\Request\Dimension;

class DataProvider 
{

    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * Category factory
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * DataProvider constructor.
     * @param ResourceConnection $resource
     * @param ScopeResolverInterface $scopeResolver
     * @param Resolver $layerResolver
     */
    public function __construct(
        ResourceConnection $resource,
        ScopeResolverInterface $scopeResolver,
        Resolver $layerResolver
    ) {
        $this->resource = $resource;
        $this->scopeResolver = $scopeResolver;
        $this->layer = $layerResolver->get();
    }

    /**
     * @param \Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation\DataProvider $subject
     * @param callable|\Closure $proceed
     * @param BucketInterface $bucket
     * @param Dimension[] $dimensions
     *
     * @param Table $entityIdsTable
     * @return Select
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetDataSet(
        \Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation\DataProvider $subject,
        \Closure $proceed,
        BucketInterface $bucket,
        array $dimensions,
        Table $entityIdsTable
    ) {
      
        if ($bucket->getField() == 'category_ids') {

            $currentScopeId = $this->scopeResolver->getScope($dimensions['scope']->getValue())->getId();
            $currentCategory = $this->layer->getCurrentCategory();

            $derivedTable = $this->resource->getConnection()->select();
            $derivedTable->from(
                ['main_table' => $this->resource->getTableName('catalog_category_product_index')],
                [
                    'value' => 'category_id'
                ]
            )->where('main_table.store_id = ?', $currentScopeId);
            $derivedTable->joinInner(
                ['entities' => $entityIdsTable->getName()],
                'main_table.product_id  = entities.entity_id',
                []
            );

            if (!empty($currentCategory)) {
                $derivedTable->join(
                    ['category' => $this->resource->getTableName('catalog_category_entity')],
                    'main_table.category_id = category.entity_id',
                    []
                )->where('`category`.`path` LIKE ?', $currentCategory->getPath() . '%')
                    ->where('`category`.`level` > ?', $currentCategory->getLevel());
            }
            
            $approvalValue = '('.$this->getAllowState(true).')';
            $derivedTable->join(
                    ['at_approval'=>$this->resource->getTableName('catalog_product_entity_int')],
                    "at_approval.entity_id = entities.entity_id AND at_approval.attribute_id = '".$this->getIdOfAttributeCode('catalog_product','approval')."'"
                    ." AND at_approval.value IN ".$approvalValue." AND at_approval.store_id = '0'", //@todo dont know why need to 0
                    []
            );

            $vendorIds = $this->getAllowVendorId(true);
            if (!empty($vendorIds)) {
                $derivedTable->join(
                    ['product_entity'=>$this->resource->getTableName('catalog_product_entity')],
                    "product_entity.entity_id = entities.entity_id AND product_entity.vendor_id NOT IN (".$vendorIds.")",
                    []
                );   
            }

            $select = $this->resource->getConnection()->select();
            $select->from(['main_table' => $derivedTable]);
    

            return $select;
        }
        return $proceed($bucket, $dimensions, $entityIdsTable);
    }


    public function getIdOfAttributeCode($entityCode, $code)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Eav\Model\ResourceModel\Entity\Attribute')
            ->getIdByCode($entityCode,$code);
    }

    protected function getAllowState($reFormat=false)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $allowState = $om->create('Vnecoms\VendorsProduct\Helper\Data')->getAllowedApprovalStatus();

        if($reFormat) {
            return implode($allowState, ', ');
        }

        return $allowState;
    } 

    protected function getAllowVendorId($reFormat=false)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $allowState = $om->create('Vnecoms\Vendors\Helper\Data')->getNotActiveVendorIds();

        if($reFormat) {
            return implode($allowState, ', ');
        }

        return $allowState;
    } 
}

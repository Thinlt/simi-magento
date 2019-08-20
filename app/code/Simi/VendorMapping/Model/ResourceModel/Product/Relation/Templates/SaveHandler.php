<?php
/**
 * Copyright 2019 Magento. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Model\ResourceModel\Product\Relation\Templates;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\HydratorPool;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Aheadworks\Giftcard\Model\Product\Type\Giftcard as ProductGiftcard;
use Aheadworks\Giftcard\Api\Data\TemplateInterface;
use Aheadworks\Giftcard\Api\Data\TemplateInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel\Product\Relation\Templates
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var HydratorPool
     */
    private $hydratorPool;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

        /**
     * @var TemplateInterfaceFactory
     */
    private $templateFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     * @param HydratorPool $hydratorPool
     * @param AttributeRepositoryInterface $attributeRepository
     * @param StoreManager $storeManager
     * @param EntityManager $entityManager
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        HydratorPool $hydratorPool,
        AttributeRepositoryInterface $attributeRepository,
        StoreManager $storeManager,
        EntityManager $entityManager,
        RequestInterface $request,
        DataObjectHelper $dataObjectHelper,
        TemplateInterfaceFactory $templateFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->hydratorPool = $hydratorPool;
        $this->attributeRepository = $attributeRepository;
        $this->storeManager = $storeManager;
        $this->entityManager = $entityManager;
        $this->request = $request;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->templateFactory = $templateFactory;
    }

    /**
     *  {@inheritDoc}
     */
    public function execute($entity, $arguments = [])
    {
        $hydrator = $this->hydratorPool->getHydrator(ProductInterface::class);
        $entityData = $hydrator->extract($entity);
        if ($entityData['type_id'] !== ProductGiftcard::TYPE_CODE) {
            return $entity;
        }

        // $templates = $entity->getExtensionAttributes()->getAwGiftcardTemplates();
        $productData = $this->request->getPost('product');
        $templates = [];
        $templatesData = isset($productData[ProductAttributeInterface::CODE_AW_GC_EMAIL_TEMPLATES])
            ? $productData[ProductAttributeInterface::CODE_AW_GC_EMAIL_TEMPLATES]
            : null;

        if (!is_array($templatesData)) {
            return $templates;
        }
        foreach ($templatesData as $templateData) {
            if (empty($templateData['delete'])) {
                if (isset($templateData['image'][0])) {
                    $templateData['image'] = $templateData['image'][0]['file'];
                }
                if (isset($templateData['template'])) {
                    $templateData['value'] = $templateData['template'];
                }
                $templateDataObject = $this->templateFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $templateDataObject,
                    $templateData,
                    TemplateInterface::class
                );
                $templates[] = $templateDataObject;
            }
        }

        if (!empty($templates)) {
            $entityId = $entityData['entity_id'];
            $this->removeTemplatesByProduct($entityId);
            $this->saveNewProductTemplates($templates, $entityId);
        }
        return $entity;
    }

    /**
     * Remove templates data by product id
     *
     * @param int $entityId
     * @return int
     */
    private function removeTemplatesByProduct($entityId)
    {
        $connection = $this->getConnection();
        $table = $this->resourceConnection
            ->getTableName($this->metadataPool->getMetadata(TemplateInterface::class)->getEntityTable());

        return $connection->delete($table, ['entity_id = ?' => $entityId]);
    }

    /**
     * Save new product templates data
     *
     * @param [] $templates
     * @param int $entityId
     * @return $this
     */
    private function saveNewProductTemplates($templates, $entityId)
    {
        foreach ($templates as $template) {
            /** @var TemplateInterface $template */
            $template->setValueId('');
            $template->setEntityId($entityId);
            $this->entityManager->save($template);
        }
        return $this;
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(TemplateInterface::class)->getEntityConnectionName()
        );
    }
}

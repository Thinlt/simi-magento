<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Giftcard\Model;

use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Giftcard\Model\Giftcard as GiftcardModel;
use Aheadworks\Giftcard\Api\Data\GiftcardInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\GiftcardSearchResultsInterface;
use Aheadworks\Giftcard\Api\Data\GiftcardSearchResultsInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterface as GiftcardHistoryActionInterface;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\History\CollectionFactory as GiftcardHistoryCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class GiftcardRepository
 *
 * @package Aheadworks\Giftcard\Model
 */
class GiftcardRepository implements GiftcardRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var GiftcardFactory
     */
    private $giftcardFactory;

    /**
     * @var GiftcardHistoryCollectionFactory
     */
    private $giftcardHistoryCollectionFactory;

    /**
     * @var GiftcardInterfaceFactory
     */
    private $giftcardDataFactory;

    /**
     * @var GiftcardSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;
    
    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @var []
     */
    private $registryByCode = [];

    /**
     * @param EntityManager $entityManager
     * @param GiftcardFactory $giftcardFactory
     * @param GiftcardInterfaceFactory $giftcardDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param GiftcardSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        EntityManager $entityManager,
        GiftcardFactory $giftcardFactory,
        GiftcardInterfaceFactory $giftcardDataFactory,
        GiftcardHistoryCollectionFactory $giftcardHistoryCollectionFactory,
        DataObjectHelper $dataObjectHelper,
        ObjectFactory $objectFactory,
        DataObjectProcessor $dataObjectProcessor,
        GiftcardSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->entityManager = $entityManager;
        $this->giftcardFactory = $giftcardFactory;
        $this->giftcardDataFactory = $giftcardDataFactory;
        $this->giftcardHistoryCollectionFactory = $giftcardHistoryCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->objectFactory = $objectFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function save(GiftcardInterface $giftcard)
    {
        /** @var \Aheadworks\Giftcard\Model\Giftcard $giftcardModel */
        $giftcardModel = $this->giftcardFactory->create();
        if ($giftcardId = $giftcard->getId()) {
            $this->entityManager->load($giftcardModel, $giftcardId);
        }
        $giftcardModel->setOrigData(null, $giftcardModel->getData());
        $this->dataObjectHelper->populateWithArray(
            $giftcardModel,
            $this->dataObjectProcessor->buildOutputDataArray($giftcard, GiftcardInterface::class),
            GiftcardInterface::class
        );
        $giftcardModel->beforeSave();
        $this->entityManager->save($giftcardModel);
        $giftcard = $this->getGiftcardDataObject($giftcardModel);
        $this->registry[$giftcard->getId()] = $giftcard;
        $this->registryByCode[$giftcard->getCode()] = $giftcard;
        return $giftcard;
    }

    /**
     * {@inheritdoc}
     */
    public function get($giftcardId)
    {
        if (!isset($this->registry[$giftcardId])) {
            /** @var GiftcardInterface $giftcard */
            $giftcard = $this->giftcardDataFactory->create();
            $this->entityManager->load($giftcard, $giftcardId);
            if (!$giftcard->getId()) {
                throw NoSuchEntityException::singleField('giftcardId', $giftcardId);
            }
            $this->registry[$giftcardId] = $giftcard;
            $this->registryByCode[$giftcard->getCode()] = $giftcard;
        }
        return $this->registry[$giftcardId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByCode($giftcardCode, $websiteId = null)
    {
        if (!isset($this->registryByCode[$giftcardCode])) {
            $this->searchCriteriaBuilder->addFilter(GiftcardInterface::CODE, $giftcardCode);
            if ($websiteId) {
                $this->searchCriteriaBuilder->addFilter(GiftcardInterface::WEBSITE_ID, $websiteId);
            }
            $giftcardsByCode = $this->getList($this->searchCriteriaBuilder->create())->getItems();
            $giftcardByCode = array_shift($giftcardsByCode);
            if (empty($giftcardByCode)) {
                throw NoSuchEntityException::singleField('giftcardCode', $giftcardCode);
            }

            /** @var GiftcardInterface $giftcard */
            $giftcard = $this->giftcardDataFactory->create();
            $this->entityManager->load($giftcard, $giftcardByCode->getId());
            if (!$giftcard->getId()) {
                throw NoSuchEntityException::singleField('giftcardCode', $giftcardCode);
            }
            $this->registry[$giftcard->getId()] = $giftcard;
            $this->registryByCode[$giftcardCode] = $giftcard;
        }
        return $this->registryByCode[$giftcardCode];
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var GiftcardSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var \Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Collection $collection */
        $collection = $this->giftcardFactory->create()->getCollection();
        $this->extensionAttributesJoinProcessor->process($collection, GiftcardInterface::class);
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == 'quote') {
                    $collection->addNotApplyInQuoteFilter($filter->getValue());
                } elseif ($filter->getField() == GiftcardInterface::EXPIRE_AT
                    && $filter->getConditionType() == 'expired'
                ) {
                    $collection->addExpiredFilter($filter->getValue());
                } elseif ($filter->getField() == GiftcardInterface::DELIVERY_DATE
                    && $filter->getConditionType() == 'checkDeliveryDate'
                ) {
                    $collection->addCheckDeliveryDateFilter($filter->getValue());
                } else {
                    $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                    $fields[] = $filter->getField();
                    $conditions[] = [$condition => $filter->getValue()];
                }
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        if ($sortOrders = $searchCriteria->getSortOrders()) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }

        $collection
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());

        $giftcards = [];
        /** @var GiftcardModel $giftcardModel */
        foreach ($collection as $giftcardModel) {
            $giftcards[] = $this->getGiftcardDataObject($giftcardModel);
        }
        $searchResults->setItems($giftcards);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getListHistory(SearchCriteriaInterface $searchCriteria)
    {
        /** @var GiftcardSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);

        /** @var \Aheadworks\Giftcard\Model\ResourceModel\Giftcard\History\Collection $collection */
        $collection = $this->giftcardHistoryCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, GiftcardHistoryActionInterface::class);
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        if ($sortOrders = $searchCriteria->getSortOrders()) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }

        $collection
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());

        $histories = [];
        foreach ($collection as $giftcardHistoryModel) {
            $giftcard = $this->giftcardFactory->create()->load($giftcardHistoryModel->getData(GiftcardHistoryActionInterface::GIFTCARD_ID));
            $histories[] = array(
                GiftcardHistoryActionInterface::ID => $giftcardHistoryModel->getData(GiftcardHistoryActionInterface::ID),
                GiftcardHistoryActionInterface::GIFTCARD_ID => $giftcardHistoryModel->getData(GiftcardHistoryActionInterface::GIFTCARD_ID),
                GiftcardHistoryActionInterface::UPDATED_AT => $giftcardHistoryModel->getData(GiftcardHistoryActionInterface::UPDATED_AT),
                GiftcardHistoryActionInterface::ACTION => $giftcardHistoryModel->getData(GiftcardHistoryActionInterface::ACTION),
                GiftcardHistoryActionInterface::BALANCE_AMOUNT => $giftcardHistoryModel->getData(GiftcardHistoryActionInterface::BALANCE_AMOUNT),
                GiftcardHistoryActionInterface::BALANCE_DELTA => $giftcardHistoryModel->getData(GiftcardHistoryActionInterface::BALANCE_DELTA),
                GiftcardHistoryActionInterface::COMMENT => $giftcardHistoryModel->getData(GiftcardHistoryActionInterface::COMMENT),
                GiftcardHistoryActionInterface::COMMENT_PLACEHOLDER => $giftcardHistoryModel->getData(GiftcardHistoryActionInterface::COMMENT_PLACEHOLDER),
                GiftcardHistoryActionInterface::ACTION_TYPE => $giftcardHistoryModel->getData(GiftcardHistoryActionInterface::ACTION_TYPE),
                // GiftcardHistoryActionInterface::ENTITIES => $giftcardHistoryModel->getData(GiftcardHistoryActionInterface::ENTITIES),
                'status' => $giftcard->getStatus(),
                'code' => $giftcard->getCode(),
                'order_id' => str_replace('Applied to order #', '', $giftcardHistoryModel->getData(GiftcardHistoryActionInterface::COMMENT)),
            );
        }
        $searchResults->setItems($histories);
        return $searchResults;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(GiftcardInterface $giftcard)
    {
        return $this->deleteById($giftcard->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById($giftcardId)
    {
        $giftcard = $this->get($giftcardId);
        $this->entityManager->delete($giftcard);
        if (isset($this->registry[$giftcardId])) {
            unset($this->registry[$giftcardId]);
        }
        return true;
    }

    /**
     * Retrieves giftcard data object using Giftcard Model
     *
     * @param GiftcardModel $giftcard
     * @return GiftcardInterface
     */
    private function getGiftcardDataObject(GiftcardModel $giftcard)
    {
        /** @var GiftcardInterface $giftcardDataObject */
        $giftcardDataObject = $this->giftcardDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $giftcardDataObject,
            $giftcard->getData(),
            GiftcardInterface::class
        );
        return $giftcardDataObject;
    }
}

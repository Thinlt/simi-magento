<?php

namespace Vnecoms\VendorsCustomWithdrawal\Model\Method;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use GuzzleHttp\json_decode;
use Vnecoms\VendorsCustomWithdrawal\Model\Method;
use Vnecoms\VendorsCustomWithdrawal\Model\ResourceModel\Method\CollectionFactory;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var \Vnecoms\VendorsCustomWithdrawal\Model\ResourceModel\Method\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $methodCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $methodCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $methodCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        if (isset($items) && is_array($items)) {
            /** @var Method $method */
            foreach ($items as $method) {
                $method->setData('method_fields', is_string($method->getData('fields')) ? json_decode($method->getData('fields')) : '');
                $this->loadedData[$method->getId()] = $method->getData();
            }
        }

        $data = $this->dataPersistor->get('withdrawal_method');
        if (!empty($data)) {
            $method = $this->collection->getNewEmptyItem();
            $method->setData($data);
            $this->loadedData[$method->getId()] = $method->getData();
            $this->dataPersistor->clear('withdrawal_method');
        }
        return $this->loadedData;
    }
}

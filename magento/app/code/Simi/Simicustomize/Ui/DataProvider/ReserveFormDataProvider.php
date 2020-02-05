<?php
/**
 * Copyright 2019 magento. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\Simicustomize\Ui\DataProvider;

use Simi\Simicustomize\Model\ResourceModel\Reserve\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\Giftcard\Model\Giftcard
 */
class ReserveFormDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $dataFromForm = $this->dataPersistor->get('simi_reserve_form');
        if (!empty($dataFromForm)) {
            $data[$dataFromForm['id']] = $dataFromForm;
            $this->dataPersistor->clear('simi_reserve_form');
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            if ($id) {
                $reserves = $this->getCollection()->addFieldToFilter('id', $id)->getItems();
                /** @var \Aheadworks\Giftcard\Model\Giftcard $giftcard */
                foreach ($reserves as $reserve) {
                    if ($id == $reserve->getId()) {
                        $data[$id] = $reserve->getData();
                    }
                }
            }
        }
        return $data;
    }
}

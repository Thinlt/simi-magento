<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Model\Key;

use Vnecoms\PdfPro\Model\ResourceModel\Key\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider.
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Vnecoms\PdfPro\Model\ResourceModel\Template\CollectionFactory
     */
    protected $templateCollectionFactory;
    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\Collection
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
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $pageCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param \Vnecoms\PdfPro\Helper\Data $helper
     * @param \Vnecoms\PdfPro\Model\ResourceModel\Template\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $meta
     * @param array $data
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $pageCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Vnecoms\PdfPro\Model\ResourceModel\Template\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $meta = [],
        array $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Serialize\Serializer\Json::class);
        $this->templateCollectionFactory = $collectionFactory;
        $this->collection = $pageCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->helper = $helper;
        $this->registry = $registry;
    }

    public function getThemeData()
    {
        $collection = $this->templateCollectionFactory->create();
        $result = [];
        foreach ($collection as $theme) {
            $result[$theme->getId()] = $theme->getData();
        }

        return $result;
    }

    /**
     * Get data.
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $key = $this->registry->registry('current_key');
        if ($key) {
            $keyData = $key->getData();

            if (isset($keyData['font_data'])
                && is_string($keyData['font_data'])
                && class_exists(\Magento\Framework\Serialize\Serializer\Json::class)) {
                $keyData['font_data'] = $this->serializer->unserialize($keyData['font_data']);
            } elseif (isset($keyData['font_data']) && !is_string($keyData['font_data'])) {
                $keyData['font_data'] = unserialize($keyData['font_data']);
            } else {
                $keyData['font_data'] = [];
            }
            $keyData['theme'] = $this->getThemeData();
            $this->loadedData[$key->getId()] = $keyData;
        }

        return $this->loadedData;
    }
}


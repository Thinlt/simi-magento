<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\VendorMapping\Ui\DataProvider\About;

use Vnecoms\Vendors\Model\ResourceModel\Vendor\CollectionFactory;
use Vnecoms\Vendors\Model\ResourceModel\Vendor\Collection;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Class DataProvider
 */
class FormProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider
{
    /**
     * @var \Vnecoms\Vendors\Model\ResourceModel\Vendor\Collection
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
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_vendorsession;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $vendorCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $vendorCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \Vnecoms\Vendors\Model\Session $session,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $vendorCollectionFactory->create();
        $this->_vendorsession = $session;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
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
        $vendor = $this->_vendorsession->getVendor();
        $this->loadedData[$vendor->getId()] = [
            'id' => $vendor->getId(),
            'content' => '',
        ];
        $data = $this->dataPersistor->get('vendor_about');
        if (!empty($data)) {
            $this->loadedData[$vendor->getId()] = $data;
            $this->dataPersistor->clear('vendor_about');
        }

        return $this->loadedData;
    }
}

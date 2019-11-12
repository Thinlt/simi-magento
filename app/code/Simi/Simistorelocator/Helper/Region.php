<?php

namespace Simi\Simistorelocator\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Region extends AbstractHelper {

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    public $regionCollectionFactory;

    /**
     * Region constructor.
     * @param Context $context
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     */
    public function __construct(
    Context $context, \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
    ) {
        $this->regionCollectionFactory = $regionCollectionFactory;
        parent::__construct($context);
    }

    /**
     * error state code
     */
    const STATE_ERROR = -1;

    /**
     * @param $country_id
     * @param $state_name
     * @return int
     */
    public function validateState($country_id, $state_name) {
        $collection = $this->regionCollectionFactory->create();
        $collection->addCountryFilter($country_id);

        if ($state_name == '') {
            return self::STATE_ERROR;
        }

        if (sizeof($collection) > 0) {
            $region_id = self::STATE_ERROR;
            foreach ($collection as $region) {
                if (strcasecmp($state_name, $region->getData('name')) == 0) {
                    $region_id = $region->getId();
                    break;
                }
            }
            return $region_id;
        } else {
            return 0;
        }
    }
}

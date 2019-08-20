<?php

namespace Vnecoms\VendorsApi\Model;

use Vnecoms\VendorsApi\Helper\Data as ApiHelper;
/**
 * Vendor repository.
 */
class ProductCustomOptionTypeList implements \Vnecoms\VendorsApi\Api\ProductCustomOptionTypeListInterface
{
    /**
     * @var ApiHelper
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\ProductOptions\Config
     */
    protected $config;

    /**
     * @var \Magento\Catalog\Api\Data\ProductCustomOptionTypeInterfaceFactory
     */
    protected $factory;

    /**
     * ProductRepository constructor.
     * @param ApiHelper $helper
     * @param \Magento\Catalog\Model\ProductOptions\Config $config
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionTypeInterfaceFactory $factory
     */
    public function __construct(
        ApiHelper $helper,
        \Magento\Catalog\Model\ProductOptions\Config $config,
        \Magento\Catalog\Api\Data\ProductCustomOptionTypeInterfaceFactory $factory
    ) {
        $this->helper = $helper;
        $this->config = $config;
        $this->factory = $factory;
    }

    /**
     * Get custom option types
     * @param int $customerId
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionTypeInterface[]
     */
    public function getItems($customerId){
        $vendor = $this->helper->getVendorByCustomerId($customerId);
        if (!$vendor){
            throw new LocalizedException(__('You are not authorized.'));
        }
        $output = [];
        foreach ($this->config->getAll() as $option) {
            foreach ($option['types'] as $type) {
                if ($type['disabled']) {
                    continue;
                }
                /** @var \Magento\Catalog\Api\Data\ProductCustomOptionTypeInterface $optionType */
                $optionType = $this->factory->create();
                $optionType->setLabel(__($type['label']))
                    ->setCode($type['name'])
                    ->setGroup(__($option['label']));
                $output[] = $optionType;
            }
        }
        return $output;
    }
}

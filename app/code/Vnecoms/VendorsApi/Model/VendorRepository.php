<?php

namespace Vnecoms\VendorsApi\Model;

/**
 * Vendor repository.
 */
class VendorRepository implements \Vnecoms\VendorsApi\Api\VendorRepositoryInterface
{
    /**
     * @var \Vnecoms\VendorsApi\Helper\Data
     */
    protected $helper;

    /**
     * @var \Vnecoms\VendorsApi\Api\Data\VendorInterfaceFactory
     */
    protected $vendorDataFactory;
    
    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;
    
    /**
     * @var \Vnecoms\VendorsDashboard\Model\Graph
     */
    protected $graph;
    
    /**
     * @param \Vnecoms\VendorsApi\Helper\Data $helper
     * @param \Vnecoms\VendorsApi\Api\Data\VendorInterfaceFactory $vendorDataFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Vnecoms\VendorsDashboard\Model\Graph $graph
     */
    public function __construct(
        \Vnecoms\VendorsApi\Helper\Data $helper,
        \Vnecoms\VendorsApi\Api\Data\VendorInterfaceFactory $vendorDataFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Vnecoms\VendorsDashboard\Model\Graph $graph
    ) {
        $this->helper               = $helper;
        $this->vendorDataFactory    = $vendorDataFactory;
        $this->dataObjectHelper     = $dataObjectHelper;
        $this->graph                = $graph;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($customerId)
    {
        $customer   = $this->helper->getCustomer($customerId);
        $vendor     = $this->helper->getVendorByCustomer($customer);
        
        $vendorDataObject = $this->vendorDataFactory->create();        
        $this->dataObjectHelper->populateWithArray(
            $vendorDataObject,
            $vendor->getData(),
            \Magento\Customer\Api\Data\CustomerInterface::class
        );
        
        $vendorDataObject->setCustomerId($customerId);
        $vendorDataObject->setEmail($customer->getEmail());
        $vendorDataObject->setGroupName($vendor->getGroup()->getVendorGroupCode());
        
        return $vendorDataObject;
    }
}

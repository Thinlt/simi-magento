<?php
namespace Vnecoms\Vendors\Block\Adminhtml\Vendor\Edit\Tab\Main;

class Info extends \Magento\Backend\Block\Template
{
    protected $coreRegistry;
    
    protected $dateTime;
    
    protected $customer;
    
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->dateTime = $dateTime;
        
        return parent::__construct($context, $data);
    }
    /**
     * Retrieve customer object
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer()
    {
        if (!$this->customer) {
            $this->customer = $this->coreRegistry->registry('current_vendor');
        }
        return $this->customer;
    }
}

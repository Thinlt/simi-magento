<?php
namespace Vnecoms\Vendors\Block\Vendors\Menu;

class User extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $vendorSession;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Vnecoms\Vendors\Model\Session $vendorSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Vnecoms\Vendors\Model\Session $vendorSession,
        array $data = []
    ) {
        $this->vendorSession = $vendorSession;
        return parent::__construct($context, $data);
    }
    
    /**
     * Get the name of currently logged in vendor
     *
     * @return string
     */
    public function getVendorName()
    {
        return $this->vendorSession->getCustomer()->getName();
    }
}

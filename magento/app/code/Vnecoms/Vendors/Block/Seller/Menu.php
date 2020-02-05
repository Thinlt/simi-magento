<?php
namespace Vnecoms\Vendors\Block\Seller;

/**
 * Class View
 * @package Vnecoms\Vendors\Block\Profile\Content
 */
class Menu extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_vendorSession;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Vnecoms\Vendors\Model\Session $vendorSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vnecoms\Vendors\Model\Session $vendorSession,
        array $data = []
    ) {
    
        $this->_vendorSession = $vendorSession;
        parent::__construct($context, $data);
    }
    
    
    /**
     * Get menu items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_layout->getChildBlocks($this->getNameInLayout());
    }
    
    /**
     * Get the name of currently logged in vendor
     *
     * @return string
     */
    public function getVendorName()
    {
        return $this->_vendorSession->getCustomer()->getName();
    }
}

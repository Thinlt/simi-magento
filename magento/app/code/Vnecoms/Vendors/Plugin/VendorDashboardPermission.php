<?php
namespace Vnecoms\Vendors\Plugin;

use Magento\Framework\App\RequestInterface;

class VendorDashboardPermission
{
    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $_vendorSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $_redirect;
    
    public function __construct(
        \Vnecoms\Vendors\Model\Session $vendorSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect
    ) {
        $this->_vendorSession = $vendorSession;
        $this->messageManager = $messageManager;
        $this->_redirect = $redirect;
    }

    /**
     * Check if resource for which access is needed has self permissions defined in webapi config.
     *
     * @param \Magento\Framework\Authorization $subject
     * @param callable $proceed
     * @param string $resource
     * @param string $privilege
     *
     * @return bool true If resource permission is self, to allow
     * customer access without further checks in parent method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Vnecoms\Vendors\Controller\AbstractAction $subject,
        \Closure $proceed,
        RequestInterface $request
    ) {
        $vendor = $this->_vendorSession->getVendor();
        $error = false;
        if (!$this->_vendorSession->isLoggedIn()) {
            return $proceed($request);
        }
        
        if (!$vendor->getId()) {
            $this->messageManager->addError(__("Your account is not a seller account."));
            $error = true;
        }
        
        if ($vendor->getStatus() != \Vnecoms\Vendors\Model\Vendor::STATUS_APPROVED) {
            $this->messageManager->addError(__("Your seller's account status is %1. You can not access to this page.", $vendor->getStatusLabel()));
            $error = true;
        }
        
        if ($error) {
            $this->_redirect->redirect($subject->getResponse(), 'customer/account');
            return $subject->getResponse();
        }
        
        return $proceed($request);
    }
}

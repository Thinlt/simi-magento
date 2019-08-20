<?php

namespace Vnecoms\VendorsCredit\Controller\Vendors\Withdraw;

class Form extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_VendorsCredit::credit_withdraw';
    
    /**
     * @var \Vnecoms\VendorsCredit\Helper\Data
     */
    protected $helper;
    
    /**
     * @param \Vnecoms\Vendors\App\Action\Context $context
     * @param \Vnecoms\VendorsCredit\Helper\Data $helper
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context $context,
        \Vnecoms\VendorsCredit\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->helper = $helper;
    }
    
    /**
     * @return void
     */
    public function execute()
    {
        $this->_coreRegistry->register('step', 'withdrawal_form');
        
        $method = $this->getRequest()->getParam('method');
        $availableMethods = $this->helper->getWithdrawalMethods();
        if (!$method ||
            !isset($availableMethods[$method])
        ) {
            $this->messageManager->addError(__("Withdrawal method is not valid."));
            return $this->_redirect('*/*');
        }
        try {
            $method = $availableMethods[$method];
            if (!$method->isActive()) {
                throw new \Exception(__("Withdrawal method is not available."));
            }
            if (!$method->isEnteredMethodInfo($this->_session->getVendor()->getId())) {
                $this->messageManager->addError(__("You need to enter all of your %1 info to use this method.", $method->getTitle()));
                return $this->_redirect('config/index/edit', ['section'=>'withdrawal']);
            }
            
            $this->_coreRegistry->register('current_method', $method);
            $this->_coreRegistry->register('withdrawal_method', $method);
            $this->_initAction();
            $title = $this->_view->getPage()->getConfig()->getTitle();
            $title->prepend(__("Credit"));
            $title->prepend(__("Withdraw Funds"));
            $this->_view->renderLayout();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $this->_redirect('*/*');
        }
    }
}

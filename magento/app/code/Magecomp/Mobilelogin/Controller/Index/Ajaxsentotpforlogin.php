<?php
namespace Magecomp\Mobilelogin\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magecomp\Mobilelogin\Model\LoginotpmodelFactory;
use Magento\Framework\Controller\ResultFactory;
use Magecomp\Mobilelogin\Helper\Data as MagecompHelper;
use Magento\Store\Model\StoreManagerInterface;
class Ajaxsentotpforlogin extends \Magento\Framework\App\Action\Action
{
    protected $_modelLoginOtpFactory;
    public $_helperdata;
    public $_storeManager;

    public function __construct(
        Context $context,
        LoginotpmodelFactory $modelLoginOtpFactory,
        MagecompHelper $helperData,
        StoreManagerInterface $storeManager
    )
    {
        $this->_modelLoginOtpFactory = $modelLoginOtpFactory;
        $this->_helperdata = $helperData;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $return = $this->_helperdata->sendLoginOTPCode($this->getRequest()->get('mobile'),$this->getCurrentWebsiteId());
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($return);
        return $resultJson;

    }
    public function getCurrentWebsiteId(){
        return $this->_storeManager->getStore()->getWebsiteId();
    }
}
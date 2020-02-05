<?php
namespace Magecomp\Mobilelogin\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magecomp\Mobilelogin\Model\ForgototpmodelFactory;
use Magento\Customer\Model\CustomerFactory;
use Magecomp\Mobilelogin\Helper\Data as MagecompHelper;
use Magento\Framework\Controller\ResultFactory;

class Ajaxupdatepassotp extends \Magento\Framework\App\Action\Action
{
    protected $_ForgototpmodelFactory;
	protected $_CustomerFactory;

	public function __construct(
		Context $context,
		ForgototpmodelFactory $ForgototpmodelFactory,		
		CustomerFactory $CustomerFactory,
		MagecompHelper $helperData
	){
       $this->_ForgototpmodelFactory = $ForgototpmodelFactory;
	   $this->_CustomerFactory = $CustomerFactory;
	   $this->_helperdata = $helperData;
	    parent::__construct($context);
    }
   public function execute()
    {
		$helperData = $this->_objectManager->create('Magecomp\Mobilelogin\Helper\Data');
		$mobile = $this->getRequest()->get('mobile');
		$otp = $this->getRequest()->get('otp');
		$newpass = $this->getRequest()->get('newpass');
		$isVerify = $this->_helperdata->verfiyForgotOtp($mobile,$otp);

		if($isVerify == "true"){
			$customerCount = $this->_CustomerFactory->create()->getCollection()->addFieldToFilter("mobilenumber", $mobile);
			if(count($customerCount) == 1){
				$customer = $customerCount->getFirstItem();
				$custom = $this->_CustomerFactory->create();
				$custom = $custom->setWebsiteId($helperData->getWebsiteId());
				$custom = $custom->loadByEmail($customer->getEmail());
				$custom->setRpToken($customer->getRpToken());	
				$custom->setPassword($newpass);

				$customerData = $custom->getDataModel();
				$customerData->setCustomAttribute('mobilenumber', $mobile);
				$custom->updateData($customerData);
				$custom->save();
			}
			$return = "true";
			$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
			$resultJson->setData($return);
			return $resultJson;
		}
		else
		{
			$return = "false";
			$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
			$resultJson->setData($return);
			return $resultJson;
		}

    }
}
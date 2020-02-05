<?php
namespace Magecomp\Mobilelogin\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magecomp\Mobilelogin\Model\RegotpmodelFactory;
use Magento\Framework\Controller\ResultFactory;

class Ajaxcheckregotp extends \Magento\Framework\App\Action\Action
{
    protected $_modelRegOtpFactory;

    public function __construct(
        Context $context,
        RegotpmodelFactory $modelRegOtpFactory
    )
    {
        $this->_modelRegOtpFactory = $modelRegOtpFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $mobile = $this->getRequest()->get('mobile');
        $otp = $this->getRequest()->get('otp');

        $otpModels = $this->_modelRegOtpFactory->create();
        $collection = $otpModels->getCollection();
        $collection->addFieldToFilter('mobile', $mobile);
        $collection->addFieldToFilter('random_code', $otp);

        if (count($collection) == '1') {
            $data = "true";
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($data);
            return $resultJson;
        } else {
            $data = "false";
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($data);
            return $resultJson;
        }
    }
}
<?php
namespace Magecomp\Mobilelogin\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Data\Customer as CustomerData;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Customer\Model\ResourceModel\CustomerFactory as CustomerResourceFactory;

class Updatemobilenumber extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
	protected $customerFactory;
	protected $customerData;
	protected $customer;
	protected $customerResourceFactory;
	protected $customerResource;
    public function __construct(
		Context $context,
		CustomerFactory $customerFactory,
		Customer $customer,
		CustomerData $customerData,
		CustomerResource $customerResource,
		CustomerResourceFactory $customerResourceFactory,
		 $data = array()
		)
    {
 	    parent::__construct($context);

		$this->customerFactory	= $customerFactory;
		$this->customer	= $customer;
		$this->customerData	= $customerData;
		$this->customerResourceFactory = $customerResourceFactory;
		$this->customerResource = $customerResource;
       
    }
    public function execute()
    {
		$mobile = (string)$this->getRequest()->get('mobile');
		$customerId = (string)$this->getRequest()->get('userId');
		$this->customerData = $this->customer->getDataModel();
		$this->customerData->setId($customerId);
		$this->customerData->setCustomAttribute('mobilenumber', $mobile);
		$this->customer->updateData($this->customerData);
		$this->customerResource = $this->customerResourceFactory->create();
		if ($mobile != "") {
		    $this->customerResource->saveAttribute($this->customer, 'mobilenumber');
		}
		$this->messageManager->addSuccess("Mobile Number Update successfully");

		$data = 1; 	   
		$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
		$resultJson->setData($data);
		return $resultJson;
        
    }
}
<?php 
namespace Magecomp\Mobilelogin\Helper;
use Magecomp\Mobilelogin\Model\RegotpmodelFactory;
use Magecomp\Mobilelogin\Model\LoginotpmodelFactory;
use Magecomp\Mobilelogin\Model\ForgototpmodelFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	const XML_PATH_EMAIL_ADMIN_QUOTE_SENDER = 'mobilelogin/generalsettings/adminemailsender';
	const XML_PATH_EMAIL_ADMIN_QUOTE_NOTIFICATION = 'mobilelogin/generalsettings/adminemailtemplate';
	const XML_PATH_EMAIL_ADMIN_NAME = 'Admin';
	const XML_PATH_EMAIL_ADMIN_EMAIL = 'mobilelogin/generalsettings/adminmailreceiver';
	const MOBILELOGIN_MODULEOPTION_ENABLE = 'mobilelogin/moduleoption/enable';
	const MOBILELOGIN_GENERALSETTINGS_LOGINNOTIFY = 'mobilelogin/generalsettings/loginnotify';
	const MOBILELOGIN_GENERALSETTINGS_OTP = 'mobilelogin/generalsettings/otp';
	const MOBILELOGIN_GENERALSETTINGS_OTPTYPE = 'mobilelogin/generalsettings/otptype';
	const MOBILELOGIN_FORGOTOTPSEND_MESSAGE = 'mobilelogin/forgototpsend/message';
	const MOBILELOGIN_OTPSEND_MESSAGE = 'mobilelogin/otpsend/message';
	const MOBILELOGIN_LOGINOTPSEND_MESSAGE = 'mobilelogin/loginotpsend/message';
	const MOBILELOGIN_GENERAL_AUTHKEY = 'mobilelogin/general/authkey';
	const MOBILELOGIN_GENERAL_ROUTTYPE = 'mobilelogin/general/routtype';
	const MOBILELOGIN_GENERAL_PASSWORD = 'mobilelogin/general/password';
	const MOBILELOGIN_GENERAL_APIURL = 'mobilelogin/general/apiurl';
	const MOBILELOGIN_GENERAL_SENDERID = 'mobilelogin/general/senderid';
	const MOBILELOGIN_GENERALSETTINGS_LOGINTYPE = 'mobilelogin/generalsettings/logintype';
    const MOBILELOGIN_DESIGN_TEMPLATE  =  'mobilelogin/design/template';
	const MOBILELOGIN_DESIGN_MAINLAYOUT	= 'mobilelogin/design/mainlayout';
	const MOBILELOGIN_DESIGN_LAYOUT  =	'mobilelogin/design/layout';
	const MOBILELOGIN_DESIGN_IMAGE = 'mobilelogin/design/image';

	
	protected $_storeTime;
    protected $_storeManager;
	protected $_modelRegOtpFactory;
	protected $_modelLoginOtpFactory;
	protected $_modelForgotOtpFactory;
	protected $customerCollection;
	protected $inlineTranslation;
	protected $transportBuilder;
	protected $apicall;
	
	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		RegotpmodelFactory $modelRegOtpFactory,
		\Magento\Customer\Model\ResourceModel\Customer\Collection $customerCollection,		
		\Magento\Framework\ObjectManagerInterface $objectManager,
		LoginotpmodelFactory $modelLoginOtpFactory,
		ForgototpmodelFactory $modelForgotOtpFactory,
		StateInterface $inlineTranslation,
		TransportBuilder $transportBuilder,
		Apicall $apicall
        )
	{
		$this->scopeConfig = $scopeConfig;
		$this->_storeManager = $storeManager;
		$this->_modelRegOtpFactory = $modelRegOtpFactory;
		$this->objectManager = $objectManager;
		$this->customerCollection = $customerCollection;
		$this->_modelLoginOtpFactory = $modelLoginOtpFactory;
		$this->_modelForgotOtpFactory = $modelForgotOtpFactory;
		$this->inlineTranslation = $inlineTranslation;
		$this->transportBuilder = $transportBuilder;
		$this->apicall = $apicall;
	}
	public function isEnable()
	{
		return $this->scopeConfig->getValue(
            self::MOBILELOGIN_MODULEOPTION_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function getBlockClassData()
	{
		return "hello";
	}
	public function getLayoutType()
	{
		if( $this->scopeConfig->getValue(self::MOBILELOGIN_DESIGN_MAINLAYOUT,\Magento\Store\Model\ScopeInterface::SCOPE_STORE)== 'ultimatelayout'){
			return 1;
		}
	}
	public function getImageType()
	{
		if ($this->scopeConfig->getValue(self::MOBILELOGIN_DESIGN_LAYOUT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 'template') {
			return 1;
		}
		else{
			return 0;
		}
	}
	public function getTemplateImage()
	{
		if( $this->scopeConfig->getValue(self::MOBILELOGIN_DESIGN_MAINLAYOUT,\Magento\Store\Model\ScopeInterface::SCOPE_STORE)!= 'ultimatelayout'){
			return "";
		}
		if($this->scopeConfig->getValue(self::MOBILELOGIN_DESIGN_LAYOUT,\Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 'template'){
			return $image =  $this->scopeConfig->getValue(
				self::MOBILELOGIN_DESIGN_TEMPLATE,
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
			);
		}
		else if($this->scopeConfig->getValue(self::MOBILELOGIN_DESIGN_LAYOUT,\Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 'image'){
			return $image =  $this->scopeConfig->getValue(
				self::MOBILELOGIN_DESIGN_IMAGE,
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
			);
		}
	}
	public function isEnableLoginEmail()
	{
		return $this->scopeConfig->getValue(
            self::MOBILELOGIN_GENERALSETTINGS_LOGINNOTIFY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function generateRandomString()
	{
		$length = $this->getOtpStringlenght();
		if($this->getOtpStringtype() == "N"){
			$randomString = substr(str_shuffle("0123456789"), 0, $length);
		}
		else{
			$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);	
		}

		return $randomString;
	}
	public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
	public function getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }
	 public function getStoreUrl($fromStore = true)
    {
        return $this->_storeManager->getStore()->getUrl();
    }
	 public function getStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }
	
	public function getOtpStringlenght()
	{
		return $this->scopeConfig->getValue(
            self::MOBILELOGIN_GENERALSETTINGS_OTP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function getOtpStringtype()
	{
		return $this->scopeConfig->getValue(
            self::MOBILELOGIN_GENERALSETTINGS_OTPTYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function getNotificatonEnable()
	{
		return $this->scopeConfig->getValue(
           self::MOBILELOGIN_GENERALSETTINGS_LOGINNOTIFY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function getForgotOtpTemplate()
	{
		return $this->scopeConfig->getValue(
            self::MOBILELOGIN_FORGOTOTPSEND_MESSAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function getRegOtpTemplate()
	{
		return $this->scopeConfig->getValue(
			self::MOBILELOGIN_OTPSEND_MESSAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function getLoginOtpTemplate()
	{
		return $this->scopeConfig->getValue(
			self::MOBILELOGIN_LOGINOTPSEND_MESSAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function getForgotOtpMessage($randomCode)
	{
		$storeName = $this->getStoreName();
		$storeUrl = $this->getStoreUrl();
		$codes = array('{{shop_name}}','{{shop_url}}','{{random_code}}');
		$accurate = array($storeName,$storeUrl,$randomCode);
		return str_replace($codes,$accurate,$this->getForgotOtpTemplate());
	}
	public function sendOTPCode($mobile,$websiteid)
	{
	try{
		$customerData = $this->objectManager->create('\Magento\Customer\Model\Customer');
		$customer = $customerData->getCollection()
		    ->addFieldToFilter("mobilenumber", $mobile)
			->addFieldToFilter("website_id", $websiteid);
		if(count($customer) > 0){
			// valid because user can create customer account before create vendor account => do nothing
			// return "exist";
		}
	
		$otpModels = $this->_modelRegOtpFactory->create();		
		$collection = $otpModels->getCollection();
		$collection->addFieldToFilter('mobile', $mobile);
		
		$objDate = $this->objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
		$date = $objDate->gmtDate();
		$randomCode = $this->generateRandomString();
		$message = $this->getRegOtpMessage($mobile,$randomCode);
		
		if(count($collection) == 0){
		
			$otpModel = $this->_modelRegOtpFactory->create();
			$otpModel->setRandomCode($randomCode);
			$otpModel->setCreatedTime($date);	
			$otpModel->setIsVerify(0);	
			$otpModel->setMobile($mobile);	
			$otpModel->save();	
		}else{
			
			$otpModel = $this->_modelRegOtpFactory->create()->load($mobile,'mobile');
			$otpModel->setRandomCode($randomCode);
			$otpModel->setCreatedTime($date);	
			$otpModel->setIsVerify(0);	
			$otpModel->setMobile($mobile);
			$otpModel->save();		
		}
		$apiReturn = $this->curlApiCall($message,$mobile,$randomCode);
    		return $apiReturn;
	
		}catch(\Exception $e)
		{
			return "false";
		}
	}
	
	public function sendLoginOTPCode($mobile,$websiteid)
	{
	try{	
		$customerData = $this->objectManager->create('\Magento\Customer\Model\Customer');
		$customer = $customerData->getCollection()->addFieldToFilter("mobilenumber", $mobile)
			->addFieldToFilter("website_id", $websiteid);

		if(count($customer) != 1){
			return "false";
		}
		$otpModels = $this->_modelLoginOtpFactory->create();		
		$collection = $otpModels->getCollection();
		$collection->addFieldToFilter('mobile', $mobile);
		$objDate = $this->objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
		$date = $objDate->gmtDate();
		$randomCode = $this->generateRandomString();
		$message = $this->getLoginOtpMessage($mobile,$randomCode);
		if(count($collection) == 0){
		
			$otpModel = $this->_modelLoginOtpFactory->create();
			$otpModel->setRandomCode($randomCode);
			$otpModel->setCreatedTime($date);	
			$otpModel->setIsVerify(0);	
			$otpModel->setMobile($mobile);	
			$otpModel->save();	
		}else{
			
			$otpModel = $this->_modelLoginOtpFactory->create()->load($mobile,'mobile');
			$otpModel->setRandomCode($randomCode);
			$otpModel->setCreatedTime($date);	
			$otpModel->setIsVerify(0);	
			$otpModel->setMobile($mobile);
			$otpModel->save();		
		}
		$apiReturn = $this->curlApiCall($message,$mobile,$randomCode);
    	return $apiReturn;
		}catch(\Exception $e)
		{
			return "false";
		}
	}

	public function setOtpVerified($mobile){
		$otpModel = $this->_modelLoginOtpFactory->create()->load($mobile,'mobile');
		$otpModel->setIsVerify(1);
		$otpModel->save();	
	}

	public function getAuthkey()
	{
	   return $this->scopeConfig->getValue(
	   		self::MOBILELOGIN_GENERAL_AUTHKEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	
	public function getRouttype()
	{
		 return $this->scopeConfig->getValue(
		 	self::MOBILELOGIN_GENERAL_ROUTTYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function getPassword()
	{
		return $this->scopeConfig->getValue(
			self::MOBILELOGIN_GENERAL_PASSWORD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function getApiUrl()
	{
		return $this->scopeConfig->getValue(
			self::MOBILELOGIN_GENERAL_APIURL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function getSenderId()
	{
		return $this->scopeConfig->getValue(
			self::MOBILELOGIN_GENERAL_SENDERID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	
	public function getRegOtpMessage($mobile,$randomCode)
	{
		$storeName = $this->getStoreName();
		$storeUrl = $this->getStoreUrl();
		$codes = array('{{shop_name}}','{{shop_url}}','{{random_code}}');
		$accurate = array($storeName,$storeUrl,$randomCode);
		return str_replace($codes,$accurate,$this->getRegOtpTemplate());
	}
	public function getLoginOtpMessage($mobile,$randomCode)
	{
		$storeName = $this->getStoreName();
		$storeUrl = $this->getStoreUrl();
		$codes = array('{{shop_name}}','{{shop_url}}','{{random_code}}');
		$accurate = array($storeName,$storeUrl,$randomCode);
		return str_replace($codes,$accurate,$this->getLoginOtpTemplate());
	}
	public function checkLoginOTPCode($mobile,$randome){
		
		$otpModels = $this->_modelLoginOtpFactory->create();		
		$collection = $otpModels->getCollection();
		$collection->addFieldToFilter('mobile', $mobile);
		$collection->addFieldToFilter('random_code', $randome);
		return count($collection);
		
	}
	public function verfiyForgotOtp($mobile,$otp){
		$otpModels = $this->_modelForgotOtpFactory->create();		
		$collection = $otpModels->getCollection();
		$collection->addFieldToFilter('mobile', $mobile);
		$collection->addFieldToFilter('random_code', $otp);

		if(count($collection) == 1)
		{
			$forgototp = $collection->getFirstItem();
			$forgototp->setIsVerify(1);
			$forgototp->save();
			return "true";
		}else{
			return "false";
		}
		
	}
	public function getLoginType()
	{
	   return $this->scopeConfig->getValue(
	   		self::MOBILELOGIN_GENERALSETTINGS_LOGINTYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function sendMail($remoteId,$mail,$userAgent)
	{
		// Send Mail To Admin For This
		$objDate = $this->objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
		$date = $objDate->gmtDate();

		$browser = $this->get_browser_name($userAgent);
			$this->inlineTranslation->suspend();
			$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport = $this->transportBuilder
               ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_ADMIN_QUOTE_NOTIFICATION, $storeScope))
			   ->setTemplateOptions(
                    [
                        'area' => 'frontend',
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
               ->setTemplateVars([
                    'ip'  => $remoteId,
					'email' => $mail,
					'datetime' => $date,
					'browser' => $browser
            	])
               ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_ADMIN_QUOTE_SENDER, $storeScope))
               ->addTo($mail)
               ->getTransport();

            $transport->sendMessage();
			$this->inlineTranslation->resume();
			return "true";
	}
	public function get_browser_name($user_agent)
	{
		if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
		elseif (strpos($user_agent, 'Edge')) return 'Edge';
		elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
		elseif (strpos($user_agent, 'Safari')) return 'Safari';
		elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
		elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
		
		return 'Unknown Broswer';
	}
	public function curlApiCall($message,$mobilenumbers,$randomCode)
	{
		return $this->apicall->curlApiCall($message,$mobilenumbers,$randomCode);
	}

}
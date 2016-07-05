<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Passbook Service Controller
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @author      Magestore Developer
 */
class Magestore_Loyalty_PassbookController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch()
	{
		$this->setFlag('', self::FLAG_NO_START_SESSION, 1); // Don't start standard session
		return parent::preDispatch();
	}
	
	public function indexAction()
	{
		$info = $this->getRequest()->getPathInfo();
		$pos  = strpos($info, '/index/');
		$info = substr($info, $pos + 7);
		$info = explode('/', trim($info, '/'));
		// Check Request with API
		if (count($info) < 2 || $info[0] != 'v1') {
			$this->_exit();
		}
		switch ($info[1]) {
			case 'passes':
				// Getting the Latest Version of a Pass
				// Check PassTypeID
				if ($info[2] != $this->_helper()->cardConfig('type_id')) {
					$this->_exit();
				}
				// Serial Number
				$serialNumber = $info[3];
				$customerId = str_replace($this->_helper()->getStorePrefix(), '', $serialNumber);
				$customer = Mage::getModel('customer/customer')->load($customerId);
				if (!$customer || !$customer->getId()) {
					$this->_exit();
				}
				$oldPoints = $this->_checkAuthorization($customer);
				// Check & generate passbook
				$passfile = Mage::getBaseDir('var') . DS . 'passbook' . DS . $serialNumber . '.pkpass';
				$needGenerate = false;
				if (file_exists($passfile)) {
					$rewardAccount = Mage::getModel('rewardpoints/customer')->load($customer->getId(), 'customer_id');
					if ($oldPoints != $rewardAccount->getPointBalance()) {
						$needGenerate = true;
					}
				} else {
					$needGenerate = true;
				}
				if ($needGenerate) {
					$passfile = $this->_helper()->generatePassbook($customer);
				}
				if ($passfile) {
					// Passbook is not modified
					$lastModified = gmdate('D, d M Y H:i:s', filemtime($passfile)) . ' GMT';
					if ($this->getRequest()->getHeader('If-Modified-Since') == $lastModified) {
						header("HTTP/1.1 304 Not Modified");
						exit();
					}
					// Send update data with modified time
					header('Last-Modified: ' . $lastModified, true, 200);
					header('Content-Type: application/vnd.apple.pkpass');
					header('Content-length: ' . filesize($passfile));
					if ($handle = fopen($passfile, 'r')) {
						while (($buffer = fgets($handle, 4096)) !== false) {
                    		echo $buffer;
                		}
                		if (feof($handle)) {
                			fclose($handle);
                		}
					}
					return;
				}
				break;
			case 'log':
				// Logging Errors
				$json = Zend_Json::decode($this->getRequest()->getRawBody(), Zend_Json::TYPE_OBJECT);
				if ($json && is_array($json->logs)) {
					return; // Disabled Log
					$message = implode("\n", $json->logs);
					Mage::log($message, null, 'passbook.log');
					return;
				}
				break;
			case 'devices':
				$requestMethod = $this->getRequest()->getServer('REQUEST_METHOD');
				$deviceId = $info[2];
				$passTypeId = $info[4];
				if (isset($info[5])) {
					$serialNumber = $info[5];
				}
				switch ($requestMethod) {
					case 'POST': // Register a Device
						if (!$serialNumber) {
							$this->_exit();
						}
						$customerId = str_replace($this->_helper()->getStorePrefix(), '', $serialNumber);
						$customer = Mage::getModel('customer/customer')->load($customerId);
						if (!$customer || !$customer->getId()) {
							$this->_exit();
						}
						$this->_checkAuthorization($customer);
						$model = Mage::getResourceModel('loyalty/loyalty_collection')
							->addFieldToFilter('device_id', $deviceId)
							->addFieldToFilter('pass_type_id', $passTypeId)
							->addFieldToFilter('serial_number', $serialNumber)
							->getFirstItem();
						if ($model && $model->getId()) {
							return;
						}
						$model = Mage::getModel('loyalty/loyalty');
						$model->setData('device_id', $deviceId)
							->setData('pass_type_id', $passTypeId)
							->setData('serial_number', $serialNumber);
						$json = Zend_Json::decode($this->getRequest()->getRawBody(), Zend_Json::TYPE_OBJECT);
						$model->setData('push_token', $json->pushToken);
						try {
							$model->save();
							header('HTTP/1.1 201 Created');
							exit();
						} catch (Exception $e) {
							$this->_exit();
						}
					case 'GET': // Gettings serial numbers for passes
						$collection = Mage::getResourceModel('loyalty/loyalty_collection')
							->addFieldToFilter('device_id', $deviceId)
							->addFieldToFilter('pass_type_id', $passTypeId);
						$serialNumbers = array();
						$passesUpdatedSince = $this->getRequest()->getParam('passesUpdatedSince');
						foreach ($collection as $row) {
							$passfile = Mage::getBaseDir('var') . DS . 'passbook' . DS . $row->getSerialNumber() . '.pkpass';
							if (filemtime($passfile) > $passesUpdatedSince) {
								$serialNumbers[] = $row->getSerialNumber();
							}
						}
						if (count($serialNumbers) == 0) {
							header('HTTP/1.1 204 No Content');
							exit();
						}
						echo Zend_Json::encode(array(
							'serialNumbers'	=> $serialNumbers,
							'lastUpdated'	=> '' . time()
						));
						return;
					case 'DELETE': // Unregister a Device
						if (!$serialNumber) {
							$this->_exit();
						}
						$customerId = str_replace($this->_helper()->getStorePrefix(), '', $serialNumber);
						$customer = Mage::getModel('customer/customer')->load($customerId);
						if (!$customer || !$customer->getId()) {
							$this->_exit();
						}
						$this->_checkAuthorization($customer);
						$collection = Mage::getResourceModel('loyalty/loyalty_collection')
							->addFieldToFilter('device_id', $deviceId)
							->addFieldToFilter('pass_type_id', $passTypeId)
							->addFieldToFilter('serial_number', $serialNumber);
						try {
							foreach ($collection as $row) {
								$row->delete();
							}
							return;
						} catch (Exception $e) {
							$this->_exit();
						}
				}
				break;
		}
		$this->_exit();
	}
	
	protected function _exit()
	{
		header('HTTP/1.1 302 Found');
		exit();
	}
	
	/**
	 * @return Magestore_Loyalty_Helper_Passbook
	 */
	protected function _helper()
	{
		return Mage::helper('loyalty/passbook');
	}
	
	protected function _checkAuthorization($customer)
	{
		$auth = $this->getRequest()->getServer('HTTP_AUTHORIZATION');
		if (strpos($auth, 'ApplePass ') !== 0) {
			$this->_unAuth();
		}
		if (substr($auth, 10, 32) != md5($customer->getId() . $customer->getEmail())) {
			$this->_unAuth();
		}
		return substr($auth, 42);
	}
	
	protected function _unAuth()
	{
		header('HTTP/1.0 401 Unauthorized');
		exit();
	}
}

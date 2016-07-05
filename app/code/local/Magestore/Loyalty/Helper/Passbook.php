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
 * Passbook library
 */
include_once('Passbook/PassInterface.php');
include_once('Passbook/Pass.php');
include_once('Passbook/PassFactory.php');

include_once('Passbook/Certificate/CertificateInterface.php');
include_once('Passbook/Certificate/Certificate.php');
include_once('Passbook/Certificate/P12Interface.php');
include_once('Passbook/Certificate/P12.php');
include_once('Passbook/Certificate/WWDRInterface.php');
include_once('Passbook/Certificate/WWDR.php');

include_once('Passbook/Exception/FileException.php');
include_once('Passbook/Exception/FileNotFoundException.php');

include_once('Passbook/Pass/BarcodeInterface.php');
include_once('Passbook/Pass/Barcode.php');
include_once('Passbook/Pass/BeaconInterface.php');
include_once('Passbook/Pass/Beacon.php');
include_once('Passbook/Pass/FieldInterface.php');
include_once('Passbook/Pass/Field.php');
include_once('Passbook/Pass/DateField.php');
include_once('Passbook/Pass/NumberField.php');
include_once('Passbook/Pass/ImageInterface.php');
include_once('Passbook/Pass/Image.php');
include_once('Passbook/Pass/LocationInterface.php');
include_once('Passbook/Pass/Location.php');
include_once('Passbook/Pass/StructureInterface.php');
include_once('Passbook/Pass/Structure.php');

include_once('Passbook/Type/StoreCard.php');

use Passbook\Type\StoreCard;
use Passbook\Pass\Location;
use Passbook\Pass\Barcode;
use Passbook\Pass\Structure;
use Passbook\Pass\Field;
use Passbook\Pass\NumberField;
use Passbook\Pass\Image;
use Passbook\PassFactory;

/**
 * Loyalty Passbook Helper
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @author      Magestore Developer
 */
class Magestore_Loyalty_Helper_Passbook extends Mage_Core_Helper_Abstract
{	
	public function cardConfig($field, $store = null)
	{
		return Mage::getStoreConfig('rewardpoints/passbook/' . $field, $store);
	}
	
	public function getStorePrefix()
	{
		$prefix = trim(Mage::helper('core')->encrypt('MS'), '=/+-_');
		return strtoupper($prefix);
	}
	
	public function rgbColor($hexa)
	{
		$r = hexdec(substr($hexa, 0, 2));
		$g = hexdec(substr($hexa, 2, 2));
		$b = hexdec(substr($hexa, 4));
		return "rgb($r, $g, $b)";
	}
	
	/**
	 * Generate Passbook file for customer
	 * 
	 * @param Mage_Customer_Model_Customer $customer
	 * @return string
	 */
	public function generatePassbook($customer)
	{
		$rewardAccount = Mage::getModel('rewardpoints/customer')->load($customer->getId(), 'customer_id');
		$helper        = Mage::helper('rewardpoints/point');
		$niceID        = str_pad((string)$customer->getId(), 12, '0', STR_PAD_LEFT);
		
		// Create StoreCard pass
        $pass = new StoreCard($this->getStorePrefix() . $customer->getId(), $this->cardConfig('description'));
        $pass->setWebServiceURL(Mage::getUrl('loyalty/passbook/index', array('_secure' => true)));
        $pass->setAuthenticationToken(md5($customer->getId() . $customer->getEmail()) . $rewardAccount->getPointBalance());
        
        $loc = new Location(floatval($this->cardConfig('latitude')), floatval($this->cardConfig('longitude')));
        $loc->setRelevantText($this->cardConfig('relevant'));
        $pass->addLocation($loc);
        
        if ($this->cardConfig('app_url') && $this->cardConfig('app_id')) {
            $pass->setAppLaunchURL($this->cardConfig('app_url'));
            $pass->addAssociatedStoreIdentifier(intval($this->cardConfig('app_id')));
        }
        
        $barcode = new Barcode(Barcode::TYPE_PDF_417, $niceID);
        $niceID  = substr($niceID, 0, 4) . ' ' . substr($niceID, 4, 4) . ' ' . substr($niceID, 8);
        $barcode->setAltText($niceID);
        $pass->setBarcode($barcode);
        
        $pass->setLogoText($this->cardConfig('logo_text'));
        $pass->setForegroundColor($this->rgbColor($this->cardConfig('foreground')));
        $pass->setBackgroundColor($this->rgbColor($this->cardConfig('background')));
        
        $structure = new Structure();
        
        $pointBalance = intval($rewardAccount->getPointBalance());
        if ($pointBalance > 0) {
        	$rate = Mage::getModel('rewardpoints/rate')->getRate(Magestore_RewardPoints_Model_Rate::POINT_TO_MONEY, $customer->getGroupId());
        	if ($rate && $rate->getId()) {
        		$baseAmount = $pointBalance * $rate->getMoney() / $rate->getPoints();
        		$header = new NumberField('balance', Mage::app()->getStore()->convertPrice($baseAmount, false, false));
        		$header->setLabel($this->__('BALANCE'));
        		$header->setCurrencyCode(Mage::app()->getStore()->getCurrentCurrencyCode());
        		$structure->addHeaderField($header);
        	}
        }
        
        $primary = new NumberField('points', $pointBalance);
        $primary->setLabel($this->__('AVAILABLE POINTS'));
        $structure->addPrimaryField($primary);
        
        $auxiliary = new Field('name', strtoupper($customer->getName()));
        $auxiliary->setLabel($this->__('NAME'));
        $structure->addAuxiliaryField($auxiliary);
        
        if ($this->cardConfig('terms')) {
            $back = new Field('term', $this->cardConfig('terms'));
            $back->setLabel($this->__('Terms and Conditions'));
            $structure->addBackField($back);
        }
        
        $pass->setStructure($structure);
        
        // Icon, Logo, Strip
        $icon   = $this->cardConfig('icon') ? $this->cardConfig('icon') : 'default/icon@2x.png';
        $logo   = $this->cardConfig('logo') ? $this->cardConfig('logo') : 'default/logo@2x.png';
        $strip  = $this->cardConfig('strip') ? $this->cardConfig('strip') : 'default/strip@2x.png';
        
        // Add Images
        $iconImg = new Image($this->getMediaDir($icon), 'icon');
        $pass->addImage($iconImg);
        $iconImg = new Image($this->getMediaDir($icon), 'icon');
        $iconImg->setIsRetina(true);
        $pass->addImage($iconImg);
        
        $logoImg = new Image($this->getMediaDir($logo), 'logo');
        $pass->addImage($logoImg);
        $logoImg = new Image($this->getMediaDir($logo), 'logo');
        $logoImg->setIsRetina(true);
        $pass->addImage($logoImg);
        
        $stripImg = new Image($this->getMediaDir($strip), 'strip');
        $pass->addImage($stripImg);
        $stripImg = new Image($this->getMediaDir($strip), 'strip');
        $stripImg->setIsRetina(true);
        $pass->addImage($stripImg);
        
        // Create package
        $passType = $this->cardConfig('type_id') ? $this->cardConfig('type_id') : 'pass.com.simicart.loyalty';
        $teamID   = $this->cardConfig('team_id') ? $this->cardConfig('team_id') : '88X6EP4WFV';
        $orgName  = $this->cardConfig('organization') ? $this->cardConfig('organization') : 'SimiCart';
        $p12File  = $this->cardConfig('certificate') ? $this->cardConfig('certificate') : 'default/pass.com.simicart.loyalty.p12';
        $password = $this->cardConfig('password') ? $this->cardConfig('password') : 'simicart';
        $pemFile  = $this->cardConfig('wwdr') ? $this->cardConfig('wwdr') : 'default/AppleWWDRCA.pem';
        
        $output = Mage::getBaseDir('var') . DS . 'passbook';
        // 'PASS-TYPE-IDENTIFIER', 'TEAM-IDENTIFIER', 'ORGANIZATION-NAME', '/path/to/p12/certificate', 'P12-PASSWORD', '/path/to/wwdr/certificate'
        $factory = new PassFactory($passType, $teamID, $orgName, $this->getMediaDir($p12File), $password, $this->getMediaDir($pemFile));
        $factory->setOutputPath($output);
        return $factory->package($pass);
	}
	
	public function getMediaDir($relative)
	{
		$media  = Mage::getBaseDir('media').DS.'loyalty'.DS;
		$relative = trim($relative, '/');
		return $media . str_replace('/', DS, $relative);
	}
}

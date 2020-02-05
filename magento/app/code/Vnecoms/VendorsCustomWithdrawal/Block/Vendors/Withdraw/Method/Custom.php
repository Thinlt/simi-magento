<?php

namespace Vnecoms\VendorsCustomWithdrawal\Block\Vendors\Withdraw\Method;

use GuzzleHttp\json_decode;
use Magento\Framework\App\ObjectManager;
/**
 * Vendor Notifications block
 */
class Custom extends \Vnecoms\VendorsCredit\Block\Vendors\Withdraw\Method\AbstractBlock
{
    protected $_template = 'Vnecoms_VendorsCustomWithdrawal::withdraw/method/custom.phtml';
    
    /**
     * @return \Vnecoms\VendorsCredit\Model\Withdrawal\Method\AbstractMethod
     */
    public function getMethod(){
        return $this->_coreRegistry->registry('current_method');
    }
    
    /**
     * @return boolean
     */
    public function isReviewStep(){
        return $this->_coreRegistry->registry('step') == 'review';
    }
    
    /**
     * @param array $a
     * @param array $b
     * @return number
     */
    protected function sortField($a, $b){
        if ($a['position'] == $b['position']) {
            return 0;
        }
        return ($a['position'] < $b['position']) ? -1 : 1;
    }
    
    /**
     * @return array
     */
    public function getFields(){
        $fields = json_decode($this->getMethod()->getMethodObj()->getFields(), true);
        if(!is_array($fields)) return [];
        
        usort($fields, [$this, 'sortField']);
        
        return $fields;
    }
    
    /**
     * Get Additional Info
     * @return array
     */
    public function getFieldInfo($field){
        $session = ObjectManager::getInstance()->get('Vnecoms\Vendors\Model\Session');
        $withdrawalParams = $session->getData('withdrawal_params');
        return isset($withdrawalParams['additional_info'][$field]['value'])?$withdrawalParams['additional_info'][$field]['value']:null;
    }
    
    /**
     * @return array
     */
    public function getSavedData(){
        $om = ObjectManager::getInstance();
        $methodData = $om->create('Vnecoms\VendorsCustomWithdrawal\Model\Method\Data');
        $session = $om->get('Vnecoms\Vendors\Model\Session');
        $methodDataCollection = $methodData->getCollection()
            ->addFieldToFilter('vendor_id',$session->getVendor()->getId())
            ->addFieldToFilter('method_id',$this->getMethod()->getMethodObj()->getId());
        
        if($methodDataCollection->count()){
            $methodData = $methodDataCollection->getFirstItem()->getMethodData();
            return json_decode($methodData, true);
        }
        
        return [];
    }
}

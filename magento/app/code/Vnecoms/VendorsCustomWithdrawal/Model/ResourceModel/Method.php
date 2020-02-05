<?php
namespace Vnecoms\VendorsCustomWithdrawal\Model\ResourceModel;

use GuzzleHttp\json_encode;
/**
 * Sms mysql resource
 */
class Method extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_vendor_withdrawal_method', 'method_id');
    }
    
    /**
     * @see \Magento\Framework\Model\ResourceModel\Db\AbstractDb::_beforeSave()
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $fieldsData = $object->getData('method_fields');
        if($fieldsData && is_array($fieldsData)){
            $fields = [];
            foreach($fieldsData as $field){
                $fields[] = [
                    'label' => $field['label'],
                    'input_type' => $field['input_type'],
                    'frontend_class' => $field['frontend_class'],
                    'position' => $field['position'],
                ];
            }
            
            $object->setFields(json_encode($fields));
            
        }
        return parent::_beforeSave($object);
    }
    
}

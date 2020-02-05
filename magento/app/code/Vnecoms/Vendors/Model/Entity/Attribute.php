<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model\Entity;

class Attribute extends \Magento\Customer\Model\Attribute
{
    /**
     * Detect backend storage type using frontend input type
     *
     * @param string $type frontend_input field value
     * @return string backend_type field value
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getBackendTypeByInput($type)
    {
        $field = null;
        switch ($type) {
            case 'text':
            case 'gallery':
            case 'media_image':
            case 'multiselect':
            case 'file':
                $field = 'varchar';
                break;
    
            case 'image':
            case 'textarea':
                $field = 'text';
                break;
    
            case 'date':
                $field = 'datetime';
                break;
    
            case 'select':
            case 'boolean':
                $field = 'int';
                break;
    
            case 'price':
            case 'weight':
                $field = 'decimal';
                break;
    
            default:
                break;
        }
    
        return $field;
    }
    
    /**
     * Prepare data for save
     *
     * @return Mage_Eav_Model_Entity_Attribute
     * @throws Mage_Eav_Exception
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
    
        if ($this->getFrontendInput() == 'file') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('Vnecoms\Vendors\Model\Entity\Attribute\Backend\File');
            }
        }
    
        return $this;
    }
    
    /**
     * Detect default value using frontend input type
     *
     * @param string $type frontend_input field name
     * @return string default_value field value
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getDefaultValueByInput($type)
    {
        $field = '';
        switch ($type) {
            case 'select':
            case 'gallery':
            case 'media_image':
                break;
            case 'multiselect':
                $field = null;
                break;
    
            case 'text':
            case 'price':
            case 'image':
            case 'weight':
                $field = 'default_value_text';
                break;
    
            case 'textarea':
                $field = 'default_value_textarea';
                break;
    
            case 'date':
                $field = 'default_value_date';
                break;
    
            case 'boolean':
                $field = 'default_value_yesno';
                break;
            case 'file':
                $field = 'default_value_file';
                break;
            default:
                break;
        }
    
        return $field;
    }
}

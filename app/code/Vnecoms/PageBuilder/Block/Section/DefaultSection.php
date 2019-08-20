<?php
namespace Vnecoms\PageBuilder\Block\Section;

class DefaultSection extends \Magento\Framework\View\Element\Template
{
    public function setFields($fields = []){
        foreach($fields as $field){
            if(!isset($field['class']) || !$field['is_active']) continue;
            $template = isset($field['data']['block_template'])?
                $field['data']['block_template']:
                (isset($field['block_template'])?$field['block_template']:'');
            $fieldBlockName = '';
            while(!$fieldBlockName || $this->getLayout()->getBlock($fieldBlockName)){
                $fieldBlockName = 'field_'.rand(1000,9999);
            }
            
            $fieldBlock = $this->getLayout()
                ->createBlock($field['class'],$this->getNameInLayout().'_'.$fieldBlockName)
                ->setData($field['data'])
                ->setIsActive($field['is_active'])
                ->setTemplate($template);
            if(isset($field['fields'])){
                $fieldBlock->setFields($field['fields']);
            }
            $this->setChild($field['id'], $fieldBlock);
        }
        
        return $this;
    }
}

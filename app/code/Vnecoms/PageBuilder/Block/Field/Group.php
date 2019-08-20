<?php
namespace Vnecoms\PageBuilder\Block\Field;

class Group extends \Vnecoms\PageBuilder\Block\Field\AbstractField
{
    protected $childBlocks = [];
    
    /**
     * Get field HTML
     * 
     * @param string $fieldId
     * @return string
     */
    public function getFieldHtml($fieldId){
        $fields = $this->getFields();
        if(!isset($fields[$fieldId]) || !$fields[$fieldId]) return '';
        $fieldData = $fields[$fieldId];
        if(!isset($fieldData['type'])) return '';
        if($block = $this->getChildBlock($fieldId)) return $block->toHtml();
        $fieldBlockName = '';
        while(!$fieldBlockName || $this->getLayout()->getBlock($fieldBlockName)){
            $fieldBlockName = $this->getNameInLayout().'.field_'.rand(1000,9999);
        }
        $block = $this->getLayout()
                ->createBlock($fieldData['class'],$fieldBlockName)
                ->setData($fieldData['data'])
                ->setIsActive($fieldData['is_active'])
                ->setTemplate(isset($fieldData['data']['block_template'])?$fieldData['data']['block_template']:$fieldData['block_template']);
        
        
        $this->setChild($fieldId, $block);
        return $block->toHtml();
    }
}

<?php
namespace Vnecoms\PageBuilder\Block\Field;

class ListBlock extends \Vnecoms\PageBuilder\Block\Field\AbstractField
{
    /**
     * Sorted FIelds
     * @var array
     */
    protected $sortedFields;
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Framework\View\Element\AbstractBlock::getChildHtml()
     */
    public function getChildHtml($alias = '', $useCache = true){
        if(!$this->getData('isInitChildFields')){
            $this->initChildFields();
            $this->setData('isInitChildFields', true);
        }
        return parent::getChildHtml($alias, $useCache);
    }
    
    /**
     * Get fields
     * @return Ambigous <\Vnecoms\PageBuilder\Block\Field\multitype:, multitype:>
     */
    public function getFields(){
        return $this->getSortedFields();
    }
    
    /**
     * Get sorted Fields
     * 
     * @return multitype:
     */
    public function getSortedFields(){
        if(!$this->sortedFields){
            $fields = [];
            $tmpFields = $this->getData('fields');
            while(sizeof($tmpFields)){
                $minSortOrderField = false;
                /*Find the min sort order field id*/
                foreach($tmpFields as $fieldId => $field){
                    if(!$minSortOrderField){
                        $minSortOrderField = $fieldId;
                    }else{
                        $minSortOrder = isset($tmpFields[$minSortOrderField]['data']['sortOrder'])?$tmpFields[$minSortOrderField]['data']['sortOrder']:0;
                        $fieldOrder = isset($field['data']['sortOrder'])?$field['data']['sortOrder']:0;
                        if($fieldOrder < $minSortOrder){
                            $minSortOrderField = $fieldId;
                        }
                    }
                }
                $fields[$minSortOrderField] = $tmpFields[$minSortOrderField];
                unset($tmpFields[$minSortOrderField]);
            }
            
            $this->sortedFields = $fields;
        }
        
        return $this->sortedFields;
    }
    
    /**
     * Init child fields
     */
    public function initChildFields(){
        $fields = $this->getSortedFields();
        
        foreach (array_keys($fields) as $fieldId){
            $this->initField($fieldId);
        }
    }
    
    /**
     * Init field
     * 
     * @param unknown $fieldId
     * @return string|\Vnecoms\PageBuilder\Block\Field\ListBlock
     */
    public function initField($fieldId){
        $fields = $this->getSortedFields();
        if(!isset($fields[$fieldId]) || !$fields[$fieldId]) return '';
        $fieldData = $fields[$fieldId];
        if(!isset($fieldData['type'])) return $this;
        if($block = $this->getChildBlock($fieldId)) return $this;
        $fieldBlockName = '';
        while(!$fieldBlockName || $this->getLayout()->getBlock($fieldBlockName)){
            $fieldBlockName = $this->getNameInLayout().'.field_'.rand(1000,9999);
        }
        $block = $this->getLayout()
            ->createBlock($fieldData['class'],$fieldBlockName)
            ->setData($fieldData['data'])
            ->setIsActive($fieldData['is_active'])
            ->setTemplate(isset($fieldData['data']['block_template'])?$fieldData['data']['block_template']:$fieldData['block_template']);
        if(isset($fieldData['fields'])){
            $block->setFields($fieldData['fields']);
        }
        $this->setChild($fieldId, $block);
        return $this;
    }
}

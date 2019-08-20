<?php
namespace Vnecoms\PageBuilder\Model\Template;

use GuzzleHttp\json_decode;
use Magento\Framework\App\ObjectManager;
use Vnecoms\PageBuilder\Helper\Data as PageBuilderHelper;

class Filter extends \Magento\Widget\Model\Template\Filter
{
    /**#@+
     * Construction logic regular expression
     */
    const CONSTRUCTION_VNECOMS_PAGEBUILDER_FILTER = '/<!--VNECOMS_PAGEBUILDER\s*(.*?)\s*-->/si';
    
    /**
     * @var \Vnecoms\PageBuilder\Helper\Data
     */
    protected $helper;
    
    /**
     * Filter
     * 
     * @param unknown $value
     * @throws Exception
     * @return Ambigous <string, \Magento\Framework\Phrase, string, mixed>
     */
    public function filter($value){
        if (preg_match_all(self::CONSTRUCTION_VNECOMS_PAGEBUILDER_FILTER, $value, $constructions, PREG_SET_ORDER)) {
            foreach ($constructions as $construction) {
                $callback = [$this, 'pagebuilderDirective'];
                if (!is_callable($callback)) {
                    continue;
                }
                try {
                    $replacedValue = call_user_func($callback, $construction);
                } catch (\Exception $e) {
                    throw $e;
                }
                $value = str_replace($construction[0], $replacedValue, $value);
            }
        }
        return parent::filter($value);
    }
    
    /**
     * Get page builder helper
     * 
     * @return \Vnecoms\PageBuilder\Helper\Data
     */
    public function getHelper(){
        if(!$this->helper){
            $om     = ObjectManager::getInstance();
            $this->helper = $om->create('Vnecoms\PageBuilder\Helper\Data');
        }
        return $this->helper;
    }
    
    /**
     * Process bage builder widget
     * 
     * @param array $construction
     * @return string
     */
    public function pagebuilderDirective($construction){
        $om     = ObjectManager::getInstance();
        $helper = $this->getHelper();
        $layout = $om->create('Magento\Framework\View\LayoutInterface');
        $sections = json_decode(preg_replace('/\r|\n/', '',$construction[1]), true );
        $html = '';
        if($sections) foreach($sections as $sectionData){
            $sectionType = isset($sectionData['type'])?$sectionData['type']:false;
            if(!$sectionType) continue;
            $sectionInfo = $helper->getSectionInfo($sectionType);
            if(!$sectionInfo) continue;
            $sectionBlockName = isset($sectionInfo['class'])?$sectionInfo['class']:PageBuilderHelper::DEFAULT_SECTION_CLASS;
            $sectionTemplate = isset($sectionInfo['block_template'])?$sectionInfo['block_template']:false;
            if(!$sectionTemplate) continue;
            /* Create Section Block*/
            $block = $layout->createBlock($sectionBlockName,'section_'.rand(1000,9999))->setTemplate($sectionTemplate);

           $fields = [];
            
           /* Section Fields*/
            foreach($sectionInfo['fields'] as $fieldName => $fieldData){
                if(isset($sectionData['elements'][$fieldName])){
                    $fieldData['data'] = array_merge($fieldData['data'], $sectionData['elements'][$fieldName]['data']);
                    $fieldData['is_active'] = $sectionData['elements'][$fieldName]['is_active'];
                }else{
                    continue;
                }
               
                if(!isset($fieldData['type'])) continue;
                
                $componentData = $helper->getComponentInfo($fieldData['type']);
                unset($componentData['id']);
                $fieldData = array_merge($componentData, $fieldData);
                if(isset($fieldData['fields'])){
                    $templateField = [];
                    /* For new items are added on fly, the data will be copied from template field*/
                    if(isset($fieldData['data']['templateItem'])){
                        $templateFieldId    = $fieldData['data']['templateItem'];
                        $templateField      = $sectionInfo['fields'][$fieldName]['fields'][$templateFieldId];
                    }
                    
                    $fieldData['fields'] = $this->copyData(
                        $sectionData['elements'][$fieldName]['fields'],
                        $fieldData['fields'],
                        $templateField
                    );
                }

                $fields[$fieldName] = $fieldData;
            }
            
            $block->setFields($fields);
            
            $html .= $block->toHtml();
        }
        return $html;
    }
    
    /**
     * Copy Data
     * 
     * @param array $from
     * @param array $to
     * @param array $templateData
     * @return multitype:
     */
    public function copyData($from, $to, $templateData = []){
        $helper = $this->getHelper();
        foreach($from as $fieldId => $fromData){
            /* If the item is added on fly, copy the field data from template field*/
            if(!isset($to[$fieldId])){
                $to[$fieldId] = $templateData;
            }
            $to[$fieldId]['is_active'] = $from[$fieldId]['is_active'];
            $componentData = $helper->getComponentInfo($fromData['type']);
            $to[$fieldId] = array_merge($to[$fieldId], $componentData);

            if(isset($from[$fieldId]['data'])){
                if(!isset($to[$fieldId]['data'])) $to[$fieldId]['data'] = [];
                $to[$fieldId]['data'] = array_merge($to[$fieldId]['data'], $from[$fieldId]['data']);
            }
            
            if(isset($from[$fieldId]['fields'])){
                if(!isset($to[$fieldId]['fields'])) $to[$fieldId]['fields'] = [];
                $to[$fieldId]['fields'] = $this->copyData(
                    $from[$fieldId]['fields'],
                    $to[$fieldId]['fields']
                );
            }
        }
        
        /*Remove removed Field*/
        foreach($to as $fieldId => $data){
            if(!isset($from[$fieldId])){
                unset($to[$fieldId]);
            }
        }
        return $to;
    }
}

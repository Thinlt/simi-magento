<?php
namespace Vnecoms\PageBuilder\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DEFAULT_SECTION_CLASS         = 'Vnecoms\PageBuilder\Block\Section\DefaultSection';
    const XML_PATH_PEXELS_CATEGORIES    = 'pagebuilder/general/pexels_categories';
    const PEXELS_API_KEY                = '563492ad6f9170000100000149ef76e5ffbb49627a1405361ec0bc52';
    
    /**
     * @var \Vnecoms\PageBuilder\Model\Component\Loader
     */
    protected $componentLoader;
    
    /**
     * @var \Vnecoms\PageBuilder\Model\Section\Loader
     */
    protected $sectionLoader;
    
    /**
     * Section Data
     * @var array
     */
    protected $sectionData = [];
    
    /**
     * Section Type
     * @var array
     */
    protected $sectionTypes = [];
    
    /**
     * @var array
     */
    protected $componentData = [];
    
    /**
     * @param Context $context
     * @param \Vnecoms\PageBuilder\Model\Component\Loader $componentLoader
     */
    public function __construct(
        Context $context,
        \Vnecoms\PageBuilder\Model\Component\Loader $componentLoader,
        \Vnecoms\PageBuilder\Model\Section\Loader $sectionLoader
    ) {
        parent::__construct($context);
        $this->componentLoader  = $componentLoader;
        $this->sectionLoader    = $sectionLoader;
    }
    
    /**
     * sort two object
     * 
     * @param array $a
     * @param array $b
     * @return number
     */
    protected function usort($a, $b){
        $aSort = isset($a['sortOrder'])?$a['sortOrder']:0;
        $bSort = isset($b['sortOrder'])?$b['sortOrder']:0;

        if ($aSort == $bSort) {
            return 0;
        }
        return ($aSort < $bSort) ? -1 : 1;
    }
    
    /**
     * Get field components
     * 
     * @return array
     */
    public function getComponents(){
        if(!$this->componentData){
            $this->componentData = $this->componentLoader->getData();
        }
        return $this->componentData;
    }
    
    /**
     * Get Component Info
     * 
     * @param string $componentName
     * @return boolean|array
     */
    public function getComponentInfo($componentName){
        $comData = $this->getComponents();
        return isset($comData[$componentName])?$comData[$componentName]:false;
    }
    
    /**
     * Get field components
     *
     * @return array
     */
    public function getSections($resources=[]){
        $key = implode("_", $resources);
        if(!isset($this->sectionData[$key])){
            $sectionConfig = $this->sectionLoader->getData();
            $sectionTypes = $this->getSectionTypes($resources);
            $sectionTypeIds = [];
            foreach($sectionTypes as $type){
                $sectionTypeIds[] = $type['id'];
            }
            $sections = [];
            if(isset($sectionConfig['sections']) && is_array($sectionConfig['sections'])){
                foreach($sectionConfig['sections'] as $sectionId=>$section){
                    if(!in_array($section['type'], $sectionTypeIds)) continue;
                    $sections[$sectionId] = $section;
                }
            }
            $this->sectionData[$key] = $sections;
        }
        return $this->sectionData[$key];
    }
    
    /**
     * Get section info
     * 
     * @param string $sectionName
     * @return boolean|array
     */
    public function getSectionInfo($sectionName){
        $sections = $this->getSections();
        return isset($sections[$sectionName])?$sections[$sectionName]:false;
    }
    
    
    /**
     * Get Section Types
     * 
     * @param array $resources
     * @return multitype:
     */
    public function getSectionTypes($resources=[]){
        $key = implode("_", $resources);
        if(!isset($this->sectionTypes[$key])){
            $sectionConfig = $this->sectionLoader->getData();
            $types = [];
            if(isset($sectionConfig['types']) && is_array($sectionConfig['types'])){
                foreach($sectionConfig['types'] as $sectionType){
                    if(sizeof($resources) && !in_array($sectionType['resource'], $resources)) continue;
                    $types[] = $sectionType;
                }
            }
            
            usort($types, array($this,'usort'));
            $this->sectionTypes[$key] = $types;
        }
        
        return $this->sectionTypes[$key];
    }
    
    /**
     * Get Pexels Categories
     * 
     * @return array:
     */
    public function getPexelsCategories(){
        $categories = $this->scopeConfig->getValue(self::XML_PATH_PEXELS_CATEGORIES);
        $categories = explode("\n", $categories);
        return $categories;
    }
}
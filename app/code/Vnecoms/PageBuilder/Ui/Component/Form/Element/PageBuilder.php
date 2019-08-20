<?php

namespace Vnecoms\PageBuilder\Ui\Component\Form\Element;

use Magento\Framework\Data\FormFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;
use Vnecoms\PageBuilder\Helper\Data as Helper;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;

/**
 * Class ProductActions
 */
class PageBuilder extends \Magento\Ui\Component\Form\Element\AbstractElement
{
    const NAME = 'pagebuilder';
    /**
     * @var \Vnecoms\PageBuilder\Helper\Data
     */
    protected $helper;
    
    /**
     * Modifiers
     * 
     * @var array
     */
    protected $modifiers;
    
    /**
     * @param ContextInterface $context
     * @param FormFactory $formFactory
     * @param ConfigInterface $wysiwygConfig
     * @param Helper $helper
     * @param array $components
     * @param array $data
     * @param array $modifiers
     */
    public function __construct(
        ContextInterface $context,
        FormFactory $formFactory,
        ConfigInterface $wysiwygConfig,
        Helper $helper,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        array $modifiers = [],
        $path = 'vnecoms_pagebuilder/media'
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->helper = $helper;
        $om = ObjectManager::getInstance();
        $assetRepo = $om->get('Magento\Framework\View\Asset\Repository');
        $storeManager = $om->get('Magento\Store\Model\StoreManagerInterface');
        $pbResource = isset(
            $data['config']['pbResource']) &&
            is_array($data['config']['pbResource']
        )?$data['config']['pbResource']:[];
        $data['config']['sectionTypes']     = $this->helper->getSectionTypes($pbResource);
        $data['config']['sectionsData']     = $this->helper->getSections($pbResource);
        $data['config']['fieldsData']       = $this->helper->getComponents();
        $data['config']['baseMediaUrl']     = $storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $data['config']['baseStaticUrl']    = $assetRepo->getUrlWithParams('',[]).'/';
        $data['config']['uploadUrl']        = $this->urlBuilder->getUrl('pagebuilder/image/upload');
        $data['config']['removeUrl']        = $this->urlBuilder->getUrl('pagebuilder/image/remove');
        $data['config']['downloadUrl']        = $this->urlBuilder->getUrl('pagebuilder/image/download');
        $data['config']['validateUrl']      = $this->urlBuilder->getUrl('pagebuilder/image/validate');
        $data['config']['media']            = $this->getMyImages($path);
        $data['config']['defaultSectionType']   = $data['config']['sectionTypes'][0];
        $data['config']['pexelsCategories'] = $this->helper->getPexelsCategories();
        $data['config']['pexelsAPI']        = \Vnecoms\PageBuilder\Helper\Data::PEXELS_API_KEY;
        
        usort($modifiers, array($this,'usort'));
        $this->modifiers = $modifiers;
        $data = $this->process($data);
        
        return \Magento\Ui\Component\AbstractComponent::__construct($context, $components, $data);
    }
    
    /**
     * Process data
     * 
     * @param array $data
     * @return array
     */
    public function process($data = []){
        foreach($this->modifiers as $modifier){
            if(
                !is_array($modifier) ||
                !isset($modifier['class']) ||
                !class_exists($modifier['class'])
            ) continue;
        
            $modifier = ObjectManager::getInstance()->create($modifier['class']);
            if(!($modifier instanceof \Vnecoms\PageBuilder\Model\Modifier\ModifierInterface)) continue;
        
            $data = $modifier->process($data);
        }
        return $data;
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
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }
    
    /**
     * Get uploaded images of current vendor
     *
     * @return multitype:multitype:string NULL
     */
    public function getMyImages($path)
    {
        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $om = ObjectManager::getInstance();
        $storeManager = $om->get('\Magento\Store\Model\StoreManagerInterface');
        $mediaDirectory = $om->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
    
        $destinationFolder = $mediaDirectory->getAbsolutePath($path);
        $this->_createDestinationFolder($destinationFolder);
        $dir = new \DirectoryIterator($destinationFolder);
        $images = [];
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $fileName = $fileinfo->getFilename();
                $images[] = [
                    'name' => $fileName,
                    'file' => $fileName,
                    'size' => $fileinfo->getSize(),
                    'type' => $fileinfo->getType(),
                    'url' => $storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $path.'/' . $fileName,
                    'img_type' => 'media',
                    'img_file' => $path.'/' . $fileName,
                    'last_modify' => $fileinfo->getMTime(),
                ];
            }
        }
        usort($images, [$this, 'uCompare']);
        return array_values($images);
    }
    
    public function uCompare($a, $b){
        if ($a['last_modify'] != $b['last_modify']) {
            return $a['last_modify'] > $b['last_modify'] ? -1 : 1;
        }
        
        return 0;
    }
    
    private function _createDestinationFolder($destinationFolder)
    {
        if (!$destinationFolder) {
            return $this;
        }
    
        if (substr($destinationFolder, -1) == '/') {
            $destinationFolder = substr($destinationFolder, 0, -1);
        }
    
        if (!(@is_dir($destinationFolder)
            || @mkdir($destinationFolder, 0777, true)
        )) {
            throw new \Exception("Unable to create directory '{$destinationFolder}'.");
        }
        return $this;
    }
    
}

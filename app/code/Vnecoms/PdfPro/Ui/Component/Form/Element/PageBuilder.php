<?php

namespace Vnecoms\PdfPro\Ui\Component\Form\Element;

use Magento\Framework\Data\FormFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;
use Vnecoms\PageBuilder\Helper\Data as Helper;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Class ProductActions
 */
class PageBuilder extends \Vnecoms\PageBuilder\Ui\Component\Form\Element\PageBuilder
{
    /**
     * @param ContextInterface $context
     * @param FormFactory $formFactory
     * @param ConfigInterface $wysiwygConfig
     * @param Helper $helper
     * @param array $components
     * @param array $data
     * @param array $config
     */
    public function __construct(
        ContextInterface $context,
        FormFactory $formFactory,
        ConfigInterface $wysiwygConfig,
        Helper $helper,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        array $config = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->helper = $helper;
        $om = ObjectManager::getInstance();
        $assetRepo = $om->get('Magento\Framework\View\Asset\Repository');
        $storeManager = $om->get('Magento\Store\Model\StoreManagerInterface');
        
        $sections = [];
        foreach($this->helper->getSections() as $key => $section){
            if(substr($section['type'], 0, 4) != 'pdf_') continue;
            $sections[$key] = $section;
        }
        
        $sectionTypes = [];
        foreach($this->helper->getSectionTypes() as $sectionType){
            if(substr($sectionType['id'], 0, 4) != 'pdf_') continue;
            $sectionTypes[] = $sectionType;
        }
        $data['config']['sectionsData']     = $sections;
        $data['config']['fieldsData']       = $this->helper->getComponents();
        $data['config']['sectionTypes']     = $sectionTypes;
        $data['config']['baseMediaUrl']     = $storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $data['config']['baseStaticUrl']    = $assetRepo->getUrlWithParams('',[]).'/';
        $data['config']['uploadUrl']        = $this->urlBuilder->getUrl('pagebuilder/image/upload');
        $data['config']['removeUrl']        = $this->urlBuilder->getUrl('pagebuilder/image/remove');
        $data['config']['media']            = $this->getMyImages();
        $data['config']['defaultSectionType']   = $data['config']['sectionTypes'][0];
        
        return \Magento\Ui\Component\AbstractComponent::__construct($context, $components, $data);
    }
}

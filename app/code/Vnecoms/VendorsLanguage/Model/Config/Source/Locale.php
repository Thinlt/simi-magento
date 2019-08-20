<?php
namespace Vnecoms\VendorsLanguage\Model\Config\Source;

class Locale extends \Magento\Config\Model\Config\Source\Locale
{
    /**
     * @var \Vnecoms\VendorsLanguage\Helper\Data
     */
    protected $adminConfig;

    /**
     * Locale constructor.
     * @param \Vnecoms\VendorsLanguage\Helper\Data $adminConfig
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     */
    public function __construct
    (
        \Vnecoms\VendorsLanguage\Helper\Data $adminConfig,
        \Magento\Framework\Locale\ListsInterface $localeLists
    )
    {
        $this->adminConfig = $adminConfig;
        parent::__construct($localeLists);
    }

    /**
     * (non-PHPdoc)
     * @see \Magento\Config\Model\Config\Source\Locale::toOptionArray()
     */
    public function toOptionArray(){
        $options = parent::toOptionArray();
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $config = $object_manager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $localeNotAllow = $config->getValue("vendors/design/locale_restriction");
        $localeNotAllow = explode(",",$localeNotAllow);
        $newOptions = [];
        $adminDefaultLanguage = $this->adminConfig->getDefaultLanguageVendor();
        foreach ($options as $option){
            if ($option['value'] == $adminDefaultLanguage){
                $defaultLangOtp = ['value' => '', 'label' =>'------ '. __("Default").' - '.$option['label'].' ------'];
                array_unshift($newOptions, $defaultLangOtp);
            }
            if(in_array($option['value'],$localeNotAllow)) continue;
            $newOptions[] = $option;
        }
        return $newOptions;
    }
}

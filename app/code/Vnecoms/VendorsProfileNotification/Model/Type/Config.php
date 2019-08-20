<?php

namespace Vnecoms\VendorsProfileNotification\Model\Type;

use Vnecoms\VendorsProfileNotification\Model\Process;
use Vnecoms\Vendors\Model\Vendor;
use Magento\Framework\Data\Form;
use Magento\Config\Model\Config\Structure\Reader as ConfigReader;
use Vnecoms\Vendors\Model\UrlInterface;
use Vnecoms\VendorsConfig\Helper\Data as VendorConfigHelper;

class Config extends AbstractType
{
    const CODE = 'type_config';
    /**
     * @var ConfigReader
     */
    protected $configReader;
    
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @var VendorConfigHelper
     */
    protected $vendorConfigHelper;
    
    /**
     * @param UrlInterface $urlBuilder
     * @param ConfigReader $configReader
     * @param VendorConfigHelper $vendorConfigHelper
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ConfigReader $configReader,
        VendorConfigHelper $vendorConfigHelper
    ) {
        $this->configReader = $configReader;
        $this->vendorConfigHelper = $vendorConfigHelper;
        parent::__construct($urlBuilder);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\AbstractType::getTitle()
     */
    public function getTitle(){
        return __('Vendor Configuration');
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\AbstractType::prepareForm()
     */
    public function prepareForm(
        Form $form,
        Process $process
    ){
        $formFieldset = $form->getElement('base_fieldset');
        $options = [];
        $config = $this->configReader->read('vendors');
        $sections = $config['config']['system']['sections'];
        
        foreach($sections as $section){
            $sectionLabel = isset($section['label'])?__($section['label']):'';
            if(is_array($section['children'])) foreach($section['children'] as $fieldset){
                $optgroupLabel = $sectionLabel?$sectionLabel.' - '.__($fieldset['label']):__($fieldset['label']);
                $values = [];
                if(is_array($fieldset['children'])) foreach($fieldset['children'] as $field){
                    $path = $section['id'].'/'.$fieldset['id'].'/'.$field['id'];
                    $values[] = [
                        'label' => isset($field['label'])?$field['label']:'',
                        'value' => $path,
                    ];
                }
                
                $options[] = [
                    'label' => $optgroupLabel,
                    'value' => $values,
                ];
            }
        }
        
        $formFieldset->addField(
            'config',
            'select',
            [
                'label' => __('Vendor Configuration'),
                'name' => 'config',
                'class' => 'process_type_field '.self::CODE,
                'note' => __('Vendor have to set value for these config fields to complete this profile process.'),
                'required' => true,
                'values' => $options
            ],
            'type'
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\AbstractType::beforeSaveProcess()
     */
    public function beforeSaveProcess(
        Process $process
    ){
        $configs = $process->getData('config');
        if(!$configs) return;
        $process->setData('additional_data', $configs);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\AbstractType::afterLoadProcess()
     */
    public function afterLoadProcess(
        Process $process
    ){
        $additionalData = $process->getData('additional_data');
        $process->setData('config', $additionalData);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\AbstractType::isCompletedProcess()
     */
    public function isCompletedProcess(Process $process, Vendor $vendor){
        $additionalData = $process->getAdditionalData();
        return $this->vendorConfigHelper->getVendorConfig($additionalData, $vendor->getId());
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\AbstractType::getUrl()
     */
    public function getUrl(Process $process){
        $additionalData = explode("/", $process->getAdditionalData());
        $url = $this->urlBuilder->getUrl(
            'config/index/edit',
            ['section' => $additionalData[0]]
        );
        $url .='#row_'.implode("_", $additionalData);;
        return $url;
    }
}

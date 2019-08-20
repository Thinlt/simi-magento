<?php
namespace Vnecoms\VendorsConfigApproval\Model\Source;

use Magento\Config\Model\Config\Structure\Reader as ConfigReader;

class Config extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var ConfigReader
     */
    protected $configReader;
    
    /**
     * @param ConfigReader $configReader
     */
    public function __construct(
        ConfigReader $configReader
    ) {
        $this->configReader = $configReader;
    }
    
    /**
     * Options array
     *
     * @var array
     */
    protected $_options = null;
    
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
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
            $this->_options = $options;
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
    
    
    /**
     * Get options as array
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}

<?php
namespace Vnecoms\VendorsProfileNotification\Model\Source;


class Type extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Vnecoms\VendorsProfileNotification\Helper\Data
     */
    protected $helper;
    
    /**
     * @param \Vnecoms\VendorsProfileNotification\Helper\Data $helper
     */
    public function __construct(
        \Vnecoms\VendorsProfileNotification\Helper\Data $helper
    ) {
        $this->helper = $helper;
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
            $this->_options = [];
            foreach($this->helper->getType() as $code=>$type){
                $this->_options[] = ['label' => $type->getTitle(), 'value' => $code];
            }
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

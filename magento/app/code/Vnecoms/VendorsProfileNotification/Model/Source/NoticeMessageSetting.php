<?php
namespace Vnecoms\VendorsProfileNotification\Model\Source;

class NoticeMessageSetting extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const SHOW_ONE_TIME = 'one_time';
    const SHOW_ALL_TIME = 'all_time';
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
            $this->_options = [
                ['label' => __("Show message one time after vendor login"), 'value' => self::SHOW_ONE_TIME],
                ['label' => __("Keep notice message showing on all pages"), 'value' => self::SHOW_ALL_TIME],
            ];
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

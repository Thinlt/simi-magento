<?php

namespace Vnecoms\VendorsProfileNotification\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Data extends AbstractHelper
{
    const XML_PROFILE_MESSAGE_ENABLED   = 'vendors/design/vendor_profile_message';
    const XML_PROFILE_MESSAGE_SETTING   = 'vendors/design/vendor_profile_message_setting';
    
    /**
     * @var array
     */
    protected $processTypes;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param array $processType
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        array $processTypes = []
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->processTypes = $processTypes;
    }
    
    /**
     * Get Process Type
     * @param string $type
     * @return \Vnecoms\VendorsProfileNotification\Model\Type\TypeInterface||array
     */
    public function getType($type=null){
        if(!$type) return $this->processTypes;
    
        return isset($this->processTypes[$type])? $this->processTypes[$type]:false;
    }
    
    /**
     * Is enabled profile message
     * 
     * @return boolean
     */
    public function isEnabledProfileMessage(){
        return (bool) $this->scopeConfig->getValue(self::XML_PROFILE_MESSAGE_ENABLED);
    }
    
    /**
     * Get profile message setting
     * 
     * @return string
     */
    public function getProfileMessageSetting(){
        return $this->scopeConfig->getValue(self::XML_PROFILE_MESSAGE_SETTING);
    }
}

<?php
namespace Vnecoms\VendorsConfig\App\Config\Initial;

use Magento\Framework\Module\Dir;

class SchemaLocator extends \Magento\Framework\App\Config\Initial\SchemaLocator
{
    /**
     * 
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param string $moduleName
     */
    public function __construct(\Magento\Framework\Module\Dir\Reader $moduleReader, $moduleName)
    {
        $this->_schema = $moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, $moduleName) . '/vendor_config.xsd';
    }
}

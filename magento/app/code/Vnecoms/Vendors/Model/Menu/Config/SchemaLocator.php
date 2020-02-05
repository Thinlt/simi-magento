<?php
/**
 * Menu configuration schema locator
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Model\Menu\Config;

use Magento\Framework\Module\Dir;

class SchemaLocator extends \Magento\Backend\Model\Menu\Config\SchemaLocator
{
    /**
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     */
    public function __construct(\Magento\Framework\Module\Dir\Reader $moduleReader)
    {
        $this->_schema = $moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, 'Vnecoms_Vendors') . '/menu.xsd';
    }
}

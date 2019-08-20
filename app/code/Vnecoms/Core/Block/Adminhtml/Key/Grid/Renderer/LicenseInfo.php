<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Core\Block\Adminhtml\Key\Grid\Renderer;

use Magento\Framework\DataObject;

class LicenseInfo extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{    
    /**
     * Renders grid column
     *
     * @param   Object $row
     * @return  string
     */
    public function render(DataObject $row)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $api = $om->create('Vnecoms\Core\Test\Api');
        return $api->renderLicenseInfo($row);
    }
}
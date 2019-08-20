<?php

namespace Vnecoms\VendorsConfig\Model\Config\Structure\Element;

use Magento\Framework\App\ObjectManager;
class Section extends \Magento\Config\Model\Config\Structure\Element\Section
{
    /**
     * Check whether section is allowed for current user
     *
     * @return bool
     */
    public function isAllowed()
    {
        if(!isset($this->_data['resource'])) return false;
        
        $permission = new \Vnecoms\Vendors\Model\AclResult();
        $eventManager = ObjectManager::getInstance()->get('Magento\Framework\Event\Manager');
        $eventManager->dispatch(
            'ves_vendor_check_acl',
            [
                'resource' => $this->_data['resource'],
                'permission' => $permission
            ]
        );
        return $permission->isAllowed();
    }
}

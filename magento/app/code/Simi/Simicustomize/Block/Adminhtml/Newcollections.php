<?php

/**
 * Adminhtml simiconnector list block
 *
 */

namespace Simi\Simicustomize\Block\Adminhtml;

class Newcollections extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_controller     = 'adminhtml_newcollections';
        $this->_blockGroup     = 'Simi_Simicustomize';
        $this->_headerText     = __('New Collections');
        $this->_addButtonLabel = __('Add New Collections');
        parent::_construct();
        if ($this->_isAllowedAction('Simi_Simiconnector::newcollections_save')) {
            $this->buttonList->update('add', 'label', __('Add New'));
        } else {
            $this->buttonList->remove('add');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return true;
    }
}

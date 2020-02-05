<?php

namespace Vnecoms\Vendors\Block\Vendors\Account\Edit\Form\Renderer;

use \Vnecoms\Vendors\Model\Vendor as VendorClass;

/**
 * Widget Instance page groups (predefined layouts group) to display on
 *
 * @method \Magento\Widget\Model\Widget\Instance getWidgetInstance()
 */
class Status extends \Vnecoms\Vendors\Block\Vendors\Widget\Form\Renderer\Fieldset\Element
{
    protected $_template = 'Vnecoms_Vendors::account/form/renderer/fieldset/status.phtml';

    /**
     * @var \Vnecoms\Vendors\Model\Source\Status
     */
    protected $_options;
    
    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vnecoms\Vendors\Model\Source\Status $status,
        array $data = []
    ) {
    
        $this->_options = $status->getOptionArray();
        return parent::__construct($context, $data);
    }
    
    /**
     * Get vendor status
     * @return string
     */
    public function getStatusLabel()
    {
        $status = $this->getElement()->getEscapedValue();
        return isset($this->_options[$status])?$this->_options[$status]:"";
    }
    
    /**
     * get Status Class
     * @return string
     */
    public function getStatusClass()
    {
        $status = $this->getElement()->getEscapedValue();
        switch ($status) {
            case VendorClass::STATUS_PENDING:
                return 'bg-orange';
            case VendorClass::STATUS_APPROVED:
                return 'bg-green';
            case VendorClass::STATUS_DISABLED:
                return 'bg-red';
        }
    }
}

<?php

namespace Vnecoms\Vendors\Block\Vendors\Account\Edit\Form\Renderer;

/**
 * Widget Instance page groups (predefined layouts group) to display on
 *
 * @method \Magento\Widget\Model\Widget\Instance getWidgetInstance()
 */
class Group extends \Vnecoms\Vendors\Block\Vendors\Widget\Form\Renderer\Fieldset\Element
{
    protected $_template = 'Vnecoms_Vendors::account/form/renderer/fieldset/group.phtml';

    /**
     * @var \Vnecoms\Vendors\Model\Group
     */
    protected $_group;
    
    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vnecoms\Vendors\Model\Group $group,
        array $data = []
    ) {
    
        $this->_group = $group;
        return parent::__construct($context, $data);
    }
    
    /**
     * Get vendor group
     * @return \Vnecoms\Vendors\Model\Group
     */
    public function getGroup()
    {
        if (!$this->_group->getId()) {
            $this->_group->load($this->getElement()->getEscapedValue());
        }
        
        return $this->_group;
    }
}

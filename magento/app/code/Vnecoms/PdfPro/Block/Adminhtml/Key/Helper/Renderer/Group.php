<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Key\Helper\Renderer;

use Magento\Customer\Model\GroupFactory as GroupFactory;

class Group extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Customer\Model\Group
     */
    protected $customerGroup;

    /**
     * Registry object.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array                          $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        GroupFactory $group,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerGroup = $group;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Render action.
     *
     * @param \Magento\Framework\Object $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $arrVal = explode(',', $value);
        if (sizeof($arrVal) == 1) {
            $array = $value;
        } else {
            $array = $arrVal;
        }
        $groups = $this->customerGroup->create()->getCollection()->addFieldToFilter('customer_group_id', array('in' => $array));
        $result = '';
        foreach ($groups as $group) {
            $result .= $group->getCustomerGroupCode().'<br />';
        }

        return $result;
    }
}

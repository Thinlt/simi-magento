<?php

namespace Vnecoms\VendorsShippingFlatRate\Model\Source\Config;

use Magento\Customer\Api\GroupManagementInterface;

class Rates extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @var GroupManagementInterface
     */
    protected $_groupManagement;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_converter;

    /**
     * @param GroupManagementInterface              $groupManagement
     * @param \Magento\Framework\Convert\DataObject $converter
     */
    public function __construct(
        GroupManagementInterface $groupManagement,
        \Magento\Framework\Convert\DataObject $converter
    ) {
        $this->_groupManagement = $groupManagement;
        $this->_converter = $converter;
    }

    /**
     * Get default customer group id.
     *
     * @return int
     */
    public function getDefaultCustomerGroup()
    {
        return 1;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface::getAllOptions()
     */
    public function getAllOptions($blankLine = false)
    {
        if (!$this->_options) {
            $groups = $this->_groupManagement->getLoggedInGroups();
            foreach ($groups as $group) {
                /*Exclude default customer group id*/
                if ($group->getId() == $this->getDefaultCustomerGroup()) {
                    continue;
                }
                $this->_options[] = [
                    'value' => $group->getId(),
                    'label' => $group->getCode(),
                ];
            }
            if ($blankLine) {
                array_unshift($this->_options, ['value' => '', 'label' => __('-- Please Select --')]);
            }
        }

        return $this->_options;
    }

    /**
     * Retrieve option array.
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->toOptionArray() as $option) {
            $_options[$option['value']] = $option['label'];
        }

        return $_options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}

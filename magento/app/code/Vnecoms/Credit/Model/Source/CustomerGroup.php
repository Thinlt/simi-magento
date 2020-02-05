<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Credit\Model\Source;

class CustomerGroup extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $_groupRepository;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_objectConverter;
    
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;
    
    /**
     * Constructor
     * 
     * @param \Magento\Framework\Convert\DataObject $objectConverter
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     */
    public function __construct(
        \Magento\Framework\Convert\DataObject $objectConverter,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
    ) {
        $this->_groupRepository = $groupRepository;
        $this->_objectConverter = $objectConverter;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $filter = $this->_searchCriteriaBuilder->create();
            $customerGroups = $this->_groupRepository->getList($filter)->getItems();
            $options = $this->_objectConverter->toOptionArray($customerGroups, 'id', 'code');
            foreach($options as $key=>$option){
                if($option['value'] == 0){
                    unset($options[$key]);
                    break;
                }
            }
            
            $this->_options = $options;
        }
        return $this->_options;
    }
}

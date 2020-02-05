<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Vendor Group Edit Block
 */
namespace Vnecoms\Vendors\Block\Adminhtml\Vendor;

class Grid extends \Magento\Backend\Block\Widget\Grid
{
//     /**
//      * Add column filtering conditions to collection
//      *
//      * @param \Magento\Backend\Block\Widget\Grid\Column $column
//      * @return $this
//      */
//     protected function _addColumnFilterToCollection($column)
//     {
//         if ($this->getCollection()) {
//             $field = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();
//             if ($column->getFilterConditionCallback()) {
//                 call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
//             } else {
//                 $condition = $column->getFilter()->getCondition();
                
//                 if ($field && isset($condition)) {
//                     if($field == 'lastname'){
//                         $this->getCollection()->getSelect()->where('lastname' , $condition);
//                     }elseif($field=='firstname'){
//                         $this->getCollection()->addFieldToFilter('customer.firstname' , $condition);
//                     }elseif($field=='email'){
//                         $this->getCollection()->addFieldToFilter('customer.email' , $condition);
//                     }else{
//                         $this->getCollection()->addFieldToFilter($field, $condition);
//                     }
                    
//                 }
//             }
//         }
//         return $this;
//     }
}

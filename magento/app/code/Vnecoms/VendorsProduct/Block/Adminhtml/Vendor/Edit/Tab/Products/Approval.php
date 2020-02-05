<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Block\Adminhtml\Vendor\Edit\Tab\Products;

use Magento\Framework\DataObject;
use Vnecoms\VendorsProduct\Model\Source\Approval as ApprovalOptions;

/**
 * Customer Credit transactions grid
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Approval extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Extended
{
    /**
     * Renders grid column
     *
     * @param   Object $row
     * @return  string
     */
    public function render(DataObject $row)
    {
        $html = parent::render($row);
        $classes = ['vendor-status'];
        $value = $row->getData($this->getColumn()->getIndex());
        switch ($value) {
            case ApprovalOptions::STATUS_NOT_SUBMITED:
                $classes[] = '' ;
                break;
            case ApprovalOptions::STATUS_PENDING:
                $classes[] = 'vendor-status-pending' ;
                break;
            case ApprovalOptions::STATUS_APPROVED:
                $classes[] = 'vendor-status-approved' ;
                break;
            case ApprovalOptions::STATUS_UNAPPROVED:
                $classes[] = 'vendor-status-disabled' ;
                break;
        }
        return '<div class="'.implode(" ", $classes).'">'.$html."</div>";
    }
}

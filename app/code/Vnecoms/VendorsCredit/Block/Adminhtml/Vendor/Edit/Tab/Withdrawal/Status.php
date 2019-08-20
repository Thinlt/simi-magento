<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsCredit\Block\Adminhtml\Vendor\Edit\Tab\Withdrawal;

use Magento\Framework\DataObject;
use Vnecoms\VendorsCredit\Model\Withdrawal;

/**
 * Customer Credit transactions grid
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Extended
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
            case Withdrawal::STATUS_PENDING:
                $classes[] = 'vendor-status-pending' ;
                break;
            case Withdrawal::STATUS_COMPLETED:
                $classes[] = 'vendor-status-approved' ;
                break;
            case Withdrawal::STATUS_CANCELED:
                $classes[] = 'vendor-status-disabled' ;
                break;
        }
        return '<div class="'.implode(" ", $classes).'">'.$html."</div>";
    }
}

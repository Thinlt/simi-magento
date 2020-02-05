<?php
/**
 * Status column for Cache grid
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Adminhtml\Vendor\Grid\Column;

use \Vnecoms\Vendors\Model\Vendor;

class Statuses extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Add to column decorated status
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return [$this, 'decorateStatus'];
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param  \Magento\Framework\Model\AbstractModel $row
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool $isExport
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        switch ($row->getStatus()) {
            case Vendor::STATUS_PENDING:
                $cell = '<span class="vendor-status vendor-status-pending"><span>' . $value . '</span></span>';
                break;
            case Vendor::STATUS_APPROVED:
                $cell = '<span class="vendor-status vendor-status-approved"><span>' . $value . '</span></span>';
                break;
            case Vendor::STATUS_DISABLED:
                $cell = '<span class="vendor-status vendor-status-disabled"><span>' . $value . '</span></span>';
                break;
            default:
                $cell = '<span class="vendor-status"><span>' . $value . '</span></span>';
        }
        return $cell;
    }
}

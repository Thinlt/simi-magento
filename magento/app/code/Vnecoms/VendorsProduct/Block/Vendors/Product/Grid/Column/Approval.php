<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Vendor product grid block
 *
 */
namespace Vnecoms\VendorsProduct\Block\Vendors\Product\Grid\Column;

use \Vnecoms\VendorsProduct\Model\Source\Approval as ApprovalSource;

class Approval extends \Vnecoms\Vendors\Block\Vendors\Widget\Grid\Column
{
    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }
    
    /**
     * Retrieve row column field value for display
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function getRowField(\Magento\Framework\DataObject $row)
    {
        $renderedValue = $this->getRenderer()->render($row);
        if ($this->getHtmlDecorators()) {
            $renderedValue = $this->_applyDecorators($renderedValue, $this->getHtmlDecorators());
        }
    
        /*
         * if column has determined callback for framing call
         * it before give away rendered value
         *
         * callback_function($renderedValue, $row, $column, $isExport)
         * should return new version of rendered value
         */
        $frameCallback = $this->getFrameCallback();
        if (is_array($frameCallback)) {
            $renderedValue = call_user_func($frameCallback, $renderedValue, $row, $this, false);
        }

        switch ($row->getData('approval')) {
            case ApprovalSource::STATUS_PENDING;
                $class = 'bg-orange';
                break;
            case ApprovalSource::STATUS_NOT_SUBMITED:
                $class = 'bg-black';
                break;
            case ApprovalSource::STATUS_APPROVED:
                $class = 'bg-green';
                break;
            case ApprovalSource::STATUS_UNAPPROVED:
                $class = 'bg-red';
                break;
            default:
                $class = 'bg-black';
        }
        return '<span class="badge text-bold '.$class.'">'.$renderedValue.'</span>';
    }
}

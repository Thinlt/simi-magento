<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Vendors\Order\Invoice\Create;

/**
 * Adminhtml invoice items grid
 */
class Items extends \Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Items
{
    /**
     * Get vendor order
     * @return \Vnecoms\VendorsSales\Model\Order
     */
    public function getVendorOrder()
    {
        return $this->_coreRegistry->registry('vendor_order');
    }
    
    /**
     * Prepare child blocks
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $onclick = "submitAndReloadArea($('invoice_item_container'),'" . $this->getUpdateUrl() . "')";
        $this->addChild(
            'update_button',
            'Magento\Backend\Block\Widget\Button',
            ['class' => 'update-button', 'label' => __('Update Qty\'s'), 'onclick' => $onclick]
        );
        $this->_disableSubmitButton = true;
        $submitButtonClass = ' disabled';
        foreach ($this->getInvoice()->getAllItems() as $item) {
            /**
             * @see bug #14839
             */
            if ($item->getQty()/* || $this->getSource()->getData('base_grand_total')*/) {
                $this->_disableSubmitButton = false;
                $submitButtonClass = '';
                break;
            }
        }
        if ($this->getOrder()->getForcedShipmentWithInvoice()) {
            $_submitLabel = __('Submit Invoice and Shipment');
        } else {
            $_submitLabel = __('Submit Invoice');
        }
        $this->addChild(
            'submit_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => $_submitLabel,
                'class' => 'save submit-button btn-primary' . $submitButtonClass,
                'onclick' => 'disableElements(\'submit-button\');$(\'edit_form\').submit()',
                'disabled' => $this->_disableSubmitButton
            ]
        );
    
        return parent::_prepareLayout();
    }
    
    /**
     * Get update url
     *
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('sales/*/updateQty', ['order_id' => $this->getVendorOrder()->getId()]);
    }


    /**
     * Check if capture operation is allowed in ACL
     *
     * @return bool
     */
    public function isCaptureAllowed()
    {
        return true;
    }
}

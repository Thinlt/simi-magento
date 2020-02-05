<?php
/**
 * @category    Magento
 * @package     Magento_Sales
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Block\Vendors\Order;

/**
 * Adminhtml sales order view
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Vnecoms\Vendors\Block\Vendors\Widget\Form\Container
{
    /**
     * Block group
     *
     * @var string
     */
    protected $_blockGroup = 'Vnecoms_VendorsSales';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Sales config
     *
     * @var \Magento\Sales\Model\Config
     */
    protected $_salesConfig;

    /**
     * Reorder helper
     *
     * @var \Magento\Sales\Helper\Reorder
     */
    protected $_reorderHelper;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        array $data = []
    ) {
        $this->_reorderHelper = $reorderHelper;
        $this->_coreRegistry = $registry;
        $this->_salesConfig = $salesConfig;
        parent::__construct($context, $data);
    }

    /**
     * Constructor
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'vendors_order';
        $this->_mode = 'view';

        parent::_construct();

        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
        $this->setId('sales_order_view');
        $this->buttonList->update('back', 'claass', 'btn btn-lg btn-default btn-github fa fa-chevron-circle-left');
        $order = $this->getOrder();
        $vendorOrder = $this->getVendorOrder();
        if (!$order) {
            return;
        }

//         if ($this->_isAllowedAction('Vnecoms_VendorsSales::actions_edit') && $order->canEdit()) {
//             $onclickJs = 'jQuery(\'#order_edit\').orderEditDialog({message: \''
//                 . $this->getEditMessage($order) . '\', url: \'' . $this->getEditUrl()
//                 . '\'}).orderEditDialog(\'showDialog\');';

//             $this->buttonList->add(
//                 'order_edit',
//                 [
//                     'label' => __('Edit'),
//                     'class' => 'edit primary',
//                     'onclick' => $onclickJs,
//                     'data_attribute' => [
//                         'mage-init' => '{"orderEditDialog":{}}',
//                     ]
//                 ]
//             );
//         }

        if ($this->_isAllowedAction('Vnecoms_VendorsSales::sales_order_action_cancel') && $vendorOrder->canCancel()) {
            $this->buttonList->add(
                'order_cancel',
                [
                    'label' => __('Cancel'),
                    'class' => 'fa fa-minus-circle btn btn-lg btn-warning btn-cancel',
                    'id' => 'order-view-cancel-button',
                    'onclick' => "confirmSetLocation('".__("Are you sure you want to cancel this order?")."', '{$this->getCancelUrl()}')"
                ]
            );
        }

//         if ($this->_isAllowedAction('Vnecoms_VendorsSales::emails') && !$order->isCanceled()) {
//             $message = __('Are you sure you want to send an order email to customer?');
//             $this->addButton(
//                 'send_notification',
//                 [
//                     'label' => __('Send Email'),
//                     'class' => 'send-email',
//                     'onclick' => "confirmSetLocation('{$message}', '{$this->getEmailUrl()}')"
//                 ]
//             );
//         }

        if ($this->_isAllowedAction('Vnecoms_VendorsSales::sales_order_action_creditmemo') && $vendorOrder->canCreditmemo()) {
            $message = __(
                'This will create an offline refund. ' .
                'To create an online refund, open an invoice and create credit memo for it. Do you want to continue?'
            );
            $onClick = "setLocation('{$this->getCreditmemoUrl()}')";
            if ($order->getPayment()->getMethodInstance()->isGateway()) {
                $onClick = "confirmSetLocation('{$message}', '{$this->getCreditmemoUrl()}')";
            }
            $this->buttonList->add(
                'order_creditmemo',
                [
                    'label' => __('Credit Memo'),
                    'onclick' => $onClick,
                    'class' => 'fa fa-rotate-left btn btn-lg btn-danger btn-credit-memo'
                ]
            );
        }


        if ($this->_isAllowedAction('Vnecoms_VendorsSales::sales_order_action_invoice') && $vendorOrder->canInvoice()) {
            $_label = $order->getForcedShipmentWithInvoice() ? __('Invoice and Ship') : __('Invoice');
            $this->buttonList->add(
                'order_invoice',
                [
                    'label' => $_label,
                    'onclick' => 'setLocation(\'' . $this->getInvoiceUrl() . '\')',
                    'class' => 'fa fa-file-text-o btn btn-lg btn-primary'
                ]
            );
        }

        if ($this->_isAllowedAction(
            'Vnecoms_VendorsSales::sales_order_action_ship'
        ) && $vendorOrder->canShip() && !$order->getForcedShipmentWithInvoice()
        ) {
            $this->buttonList->add(
                'order_ship',
                [
                    'label' => __('Ship'),
                    'onclick' => 'setLocation(\'' . $this->getShipUrl() . '\')',
                    'class' => 'fa fa-truck btn btn-lg btn-ship bg-olive'
                ]
            );
        }
    }

    /**
     * Retrieve order model object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('sales_order');
    }

    /**
     * Retrieve vendor order model object
     *
     * @return \Vnecoms\VendorsSales\Model\Order
     */
    public function getVendorOrder()
    {
        return $this->_coreRegistry->registry('vendor_order');
    }
    
    /**
     * Retrieve Order Identifier
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getOrder() ? $this->getOrder()->getId() : null;
    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $_extOrderId = $this->getOrder()->getExtOrderId();
        if ($_extOrderId) {
            $_extOrderId = '[' . $_extOrderId . '] ';
        } else {
            $_extOrderId = '';
        }
        return __(
            'Order # %1 %2 | %3',
            $this->getOrder()->getRealOrderId(),
            $_extOrderId,
            $this->formatDate(
                $this->_localeDate->date(new \DateTime($this->getOrder()->getCreatedAt())),
                \IntlDateFormatter::MEDIUM,
                true
            )
        );
    }


    /**
     * Edit URL getter
     *
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl('sales/order_edit/start', ['order_id'=>$this->getVendorOrder()->getId()]);
    }

    /**
     * Email URL getter
     *
     * @return string
     */
    public function getEmailUrl()
    {
        return $this->getUrl('sales/*/email', ['order_id'=>$this->getVendorOrder()->getId()]);
    }

    /**
     * Cancel URL getter
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getUrl('sales/*/cancel', ['order_id'=>$this->getVendorOrder()->getId()]);
    }

    /**
     * Invoice URL getter
     *
     * @return string
     */
    public function getInvoiceUrl()
    {
        return $this->getUrl('sales/order_invoice/start', ['order_id'=>$this->getVendorOrder()->getId()]);
    }

    /**
     * Credit memo URL getter
     *
     * @return string
     */
    public function getCreditmemoUrl()
    {
        return $this->getUrl('sales/order_creditmemo/start', ['order_id'=>$this->getVendorOrder()->getId()]);
    }

    /**
     * Hold URL getter
     *
     * @return string
     */
    public function getHoldUrl()
    {
        return $this->getUrl('sales/*/hold', ['order_id'=>$this->getVendorOrder()->getId()]);
    }

    /**
     * Unhold URL getter
     *
     * @return string
     */
    public function getUnholdUrl()
    {
        return $this->getUrl('sales/*/unhold', ['order_id'=>$this->getVendorOrder()->getId()]);
    }

    /**
     * Ship URL getter
     *
     * @return string
     */
    public function getShipUrl()
    {
        return $this->getUrl('sales/order_shipment/start', ['order_id'=>$this->getVendorOrder()->getId()]);
    }

    /**
     * Comment URL getter
     *
     * @return string
     */
    public function getCommentUrl()
    {
        return $this->getUrl('sales/*/comment', ['order_id'=>$this->getVendorOrder()->getId()]);
    }

    /**
     * Reorder URL getter
     *
     * @return string
     */
    public function getReorderUrl()
    {
        return $this->getUrl('sales/order_create/reorder', ['order_id'=>$this->getVendorOrder()->getId()]);
    }

    /**
     * Payment void URL getter
     *
     * @return string
     */
    public function getVoidPaymentUrl()
    {
        return $this->getUrl('sales/*/voidPayment', ['order_id'=>$this->getVendorOrder()->getId()]);
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        $permission = new \Vnecoms\Vendors\Model\AclResult();
        $this->_eventManager->dispatch(
            'ves_vendor_check_acl',
            [
                'resource' => $resourceId,
                'permission' => $permission
            ]
        );
        return $permission->isAllowed();
    }

    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getOrder() && $this->getOrder()->getBackUrl()) {
            return $this->getOrder()->getBackUrl();
        }

        return $this->getUrl('sales/*/');
    }

    /**
     * Payment review URL getter
     *
     * @param string $action
     * @return string
     */
    public function getReviewPaymentUrl($action)
    {
        return $this->getUrl('sales/*/reviewPayment', ['action' => $action]);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Framework\Phrase
     */
    protected function getEditMessage($order)
    {
        // see if order has non-editable products as items
        $nonEditableTypes = $this->getNonEditableTypes($order);
        if (!empty($nonEditableTypes)) {
            return __(
                'This order contains (%1) items and therefore cannot be edited through the admin interface. ' .
                'If you wish to continue editing, the (%2) items will be removed, ' .
                ' the order will be canceled and a new order will be placed.',
                implode(', ', $nonEditableTypes),
                implode(', ', $nonEditableTypes)
            );
        }
        return __('Are you sure? This order will be canceled and a new one will be created instead.');
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    protected function getNonEditableTypes($order)
    {
        return array_keys(
            $this->getOrder()->getResource()->aggregateProductsByTypes(
                $order->getId(),
                $this->_salesConfig->getAvailableProductTypes(),
                false
            )
        );
    }
}

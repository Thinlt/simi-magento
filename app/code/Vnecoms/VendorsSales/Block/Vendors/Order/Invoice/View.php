<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsSales\Block\Vendors\Order\Invoice;

class View extends \Vnecoms\Vendors\Block\Vendors\Widget\Form\Container
{
    /**
     * Admin session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_session;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Constructor
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _construct()
    {
        $this->_objectId = 'invoice_id';
        $this->_controller = 'adminhtml_order_invoice';
        $this->_mode = 'view';

        parent::_construct();

        $this->buttonList->remove('save');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('delete');

        if (!$this->getInvoice()) {
            return;
        }

        if ($this->_isAllowedAction(
            'Vnecoms_VendorsSales::sales_order_action_cancel'
        ) && $this->getInvoice()->canCancel() && !$this->_isPaymentReview()
        ) {
            $this->buttonList->add(
                'cancel',
                [
                    'label' => __('Cancel'),
                    'class' => 'delete',
                    'onclick' => 'setLocation(\'' . $this->getCancelUrl() . '\')'
                ]
            );
        }

        if ($this->_isAllowedAction('Vnecoms_VendorsSales::sales_send_emails')) {
            $this->addButton(
                'send_notification',
                [
                    'label' => __('Send Email'),
                    'class' => 'send-email',
                    'onclick' => 'confirmSetLocation(\'' . __(
                        'Are you sure you want to send an invoice email to customer?'
                    ) . '\', \'' . $this->getEmailUrl() . '\')'
                ]
            );
        }

        $orderPayment = $this->getInvoice()->getOrder()->getPayment();

        if ($this->_isAllowedAction('Vnecoms_VendorsSales::sales_order_action_creditmemo') && $this->getInvoice()->getOrder()->canCreditmemo()) {
            if ($orderPayment->canRefundPartialPerInvoice() &&
                $this->getInvoice()->canRefund() &&
                $orderPayment->getAmountPaid() > $orderPayment->getAmountRefunded() ||
                $orderPayment->canRefund() && !$this->getInvoice()->getIsUsedForRefund()
            ) {
                $this->buttonList->add(
                    'capture',
                    [ // capture?
                        'label' => __('Credit Memo'),
                        'class' => 'credit-memo',
                        'onclick' => 'setLocation(\'' . $this->getCreditMemoUrl() . '\')'
                    ]
                );
            }
        }

        if ($this->_isAllowedAction(
            'Vnecoms_VendorsSales::sales_order_action_capture'
        ) && $this->getInvoice()->canCapture() && !$this->_isPaymentReview()
        ) {
            $this->buttonList->add(
                'capture',
                [
                    'label' => __('Capture'),
                    'class' => 'capture',
                    'onclick' => 'setLocation(\'' . $this->getCaptureUrl() . '\')'
                ]
            );
        }

        if ($this->getInvoice()->canVoid()) {
            $this->buttonList->add(
                'void',
                [
                    'label' => __('Void'),
                    'class' => 'void',
                    'onclick' => 'setLocation(\'' . $this->getVoidUrl() . '\')'
                ]
            );
        }

        if ($this->getInvoice()->getId()) {
            $this->buttonList->add(
                'print',
                [
                    'label' => __('Print'),
                    'class' => 'fa fa-print btn btn-lg bg-olive print',
                    'onclick' => 'setLocation(\'' . $this->getPrintUrl() . '\')'
                ]
            );
        }
    }

    /**
     * Check whether order is under payment review
     *
     * @return bool
     */
    protected function _isPaymentReview()
    {
        $order = $this->getInvoice()->getOrder();
        return $order->canReviewPayment() || $order->canFetchPaymentReviewUpdate();
    }

    /**
     * Retrieve invoice model instance
     *
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function getInvoice()
    {
        return $this->_coreRegistry->registry('current_invoice');
    }

    /**
     * Retrieve vendor invoice model
     * @return \Vnecoms\VendorsSales\Model\Order\Invoice
     */
    public function getVendorInvoice(){
        return $this->_coreRegistry->registry('vendor_invoice');
    }
    
    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->getInvoice()->getEmailSent()) {
            $emailSent = __('The invoice email was sent.');
        } else {
            $emailSent = __('The invoice email wasn\'t sent.');
        }
        return __(
            'Invoice #%1 | %2 | %4 (%3)',
            $this->getInvoice()->getIncrementId(),
            $this->getInvoice()->getStateName(),
            $emailSent,
            $this->formatDate(
                $this->_localeDate->date(new \DateTime($this->getInvoice()->getCreatedAt())),
                \IntlDateFormatter::MEDIUM,
                true
            )
        );
    }

    /**
     * Get back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            'sales/order/view',
            [
                'order_id' => $this->getVendorInvoice() ? $this->getVendorInvoice()->getOrderId() : null,
                'active_tab' => 'order_invoices'
            ]
        );
    }

    /**
     * Get capture url
     *
     * @return string
     */
    public function getCaptureUrl()
    {
        return $this->getUrl('sales/*/capture', ['invoice_id' => $this->getInvoice()->getId()]);
    }

    /**
     * Get void url
     *
     * @return string
     */
    public function getVoidUrl()
    {
        return $this->getUrl('sales/*/void', ['invoice_id' => $this->getInvoice()->getId()]);
    }

    /**
     * Get cancel url
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getUrl('sales/*/cancel', ['invoice_id' => $this->getInvoice()->getId()]);
    }

    /**
     * Get email url
     *
     * @return string
     */
    public function getEmailUrl()
    {
        return $this->getUrl(
            'sales/*/email',
            ['order_id' => $this->getInvoice()->getOrder()->getId(), 'invoice_id' => $this->getInvoice()->getId()]
        );
    }

    /**
     * Get credit memo url
     *
     * @return string
     */
    public function getCreditMemoUrl()
    {
        return $this->getUrl(
            'sales/order_creditmemo/start',
            ['order_id' => $this->getVendorInvoice()->getOrder()->getId(), 'invoice_id' => $this->getInvoice()->getId()]
        );
    }

    /**
     * Get print url
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('sales/*/print', ['invoice_id' => $this->getVendorInvoice()->getId()]);
    }

    /**
     * Update back button url
     *
     * @param bool $flag
     * @return $this
     */
    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            if ($this->getInvoice()->getBackUrl()) {
                return $this->buttonList->update(
                    'back',
                    'onclick',
                    'setLocation(\'' . $this->getInvoice()->getBackUrl() . '\')'
                );
            }
            return $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $this->getUrl('sales/invoice/') . '\')');
        }
        return $this;
    }

    /**
     * Check whether is allowed action
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
}

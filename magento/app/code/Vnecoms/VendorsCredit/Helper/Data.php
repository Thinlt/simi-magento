<?php

namespace Vnecoms\VendorsCredit\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Vnecoms\VendorsCredit\Model\Escrow;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Data extends AbstractHelper
{
    const XML_PATH_NEW_WITHDRAWAL_TEMPLATE_ADMIN = 'vendors/withdrawal/new_withdrawal_email_template_admin';
    const XML_PATH_NEW_WITHDRAWAL_TEMPLATE_VENDOR = 'vendors/withdrawal/new_withdrawal_email_template_vendor';
    const XML_PATH_WITHDRAWAL_CANCELED_TEMPLATE = 'vendors/withdrawal/withdrawal_canceled_email_template';
    const XML_PATH_WITHDRAWAL_COMPLETED_TEMPLATE = 'vendors/withdrawal/withdrawal_completed_email_template';
    const XML_PATH_EMAIL_SENDER = 'vendors/withdrawal/sender_email_identity';
    const XML_PATH_ADMIN_EMAILS = 'vendors/withdrawal/admin_emails';
    
    const XML_PATH_NEW_ESCROW_TEMPLATE = 'vendors/credit/new_escrow';
    const XML_PATH_ESCROW_RELEASED_TEMPLATE = 'vendors/credit/escrow_released';
    const XML_PATH_ESCROW_CANCELED_TEMPLATE = 'vendors/credit/escrow_canceled';
    const XML_PATH_ESCROW_EMAIL_SENDER = 'vendors/credit/sender_email_identity';
    
    
    
    
    /**
     * @var \Vnecoms\Vendors\Helper\Email
     */
    protected $_emailHelper;
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Vnecoms\Vendors\Helper\Email $emailHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Vnecoms\Vendors\Helper\Email $emailHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context);
        $this->_emailHelper = $emailHelper;
        $this->_storeManager = $storeManager;
        $this->_localeDate = $localeDate;
        $this->objectManager = $objectManager;
    }
    
    
    /**
     * Send new withdrawal notification to admin
     *
     * @param \Vnecoms\VendorsCredit\Model\Withdrawal $withdrawal
     * @param \Vnecoms\Vendors\Model\Vendor $vendor
     */
    public function sendNewWithdrawalNotificationToAdmin(
        \Vnecoms\VendorsCredit\Model\Withdrawal $withdrawal,
        \Vnecoms\Vendors\Model\Vendor $vendor
    ) {
        $adminEmails = $this->scopeConfig->getValue(self::XML_PATH_ADMIN_EMAILS);
        $adminEmails = str_replace(" ", "", $adminEmails);
        if (!$adminEmails) {
            return;
        }
        $this->formatWithdrawalData($withdrawal);
        
        $adminEmails = explode(",", $adminEmails);

        try {
            $this->_emailHelper->sendTransactionEmail(
                self::XML_PATH_NEW_WITHDRAWAL_TEMPLATE_ADMIN,
                \Magento\Framework\App\Area::AREA_FRONTEND,
                self::XML_PATH_EMAIL_SENDER,
                $adminEmails,
                ['vendor' => $vendor, 'withdrawal' => $withdrawal]
            );
        } catch (\Exception $e) {
        }
    }
    
    /**
     * Send new withdrawal notification to vendor
     *
     * @param \Vnecoms\VendorsCredit\Model\Withdrawal $withdrawal
     * @param \Vnecoms\Vendors\Model\Vendor $vendor
     */
     
    public function sendNewWithdrawalNotificationToVendor(
        \Vnecoms\VendorsCredit\Model\Withdrawal $withdrawal,
        \Vnecoms\Vendors\Model\Vendor $vendor
    ) {
        $vendorEmail = $vendor->getEmail();
        $this->formatWithdrawalData($withdrawal);
        try {
            $this->_emailHelper->sendTransactionEmail(
                self::XML_PATH_NEW_WITHDRAWAL_TEMPLATE_VENDOR,
                \Magento\Framework\App\Area::AREA_FRONTEND,
                self::XML_PATH_EMAIL_SENDER,
                $vendorEmail,
                ['vendor' => $vendor, 'withdrawal' => $withdrawal]
            );
        } catch (\Exception $e) {
        }
    }
    
    /**
     * Send withdrawal canceled notification email to vendor
     *
     * @param \Vnecoms\VendorsCredit\Model\Withdrawal $withdrawal
     * @param \Vnecoms\Vendors\Model\Vendor $vendor
     */
    public function sendWithdrawalCancelledNotification(
        \Vnecoms\VendorsCredit\Model\Withdrawal $withdrawal,
        \Vnecoms\Vendors\Model\Vendor $vendor
    ) {
        $vendorEmail = $vendor->getEmail();
        $this->formatWithdrawalData($withdrawal);
        

        try {
            $this->_emailHelper->sendTransactionEmail(
                self::XML_PATH_WITHDRAWAL_CANCELED_TEMPLATE,
                \Magento\Framework\App\Area::AREA_FRONTEND,
                self::XML_PATH_EMAIL_SENDER,
                $vendorEmail,
                ['vendor' => $vendor, 'withdrawal' => $withdrawal]
            );
        } catch (\Exception $e) {
        }
    }
    
    /**
     * Send withdrawal completed notification email to vendor
     *
     * @param \Vnecoms\VendorsCredit\Model\Withdrawal $withdrawal
     * @param \Vnecoms\Vendors\Model\Vendor $vendor
     */
    public function sendWithdrawalCompletedNotification(
        \Vnecoms\VendorsCredit\Model\Withdrawal $withdrawal,
        \Vnecoms\Vendors\Model\Vendor $vendor
    ) {
        $vendorEmail = $vendor->getEmail();
        $this->formatWithdrawalData($withdrawal);
        
        try {
              $this->_emailHelper->sendTransactionEmail(
                  self::XML_PATH_WITHDRAWAL_COMPLETED_TEMPLATE,
                  \Magento\Framework\App\Area::AREA_FRONTEND,
                  self::XML_PATH_EMAIL_SENDER,
                  $vendorEmail,
                  ['vendor' => $vendor, 'withdrawal' => $withdrawal]
              );
        } catch (\Exception $e) {
        }
    }
    
    /**
     * Send Escrow Notification Email
     *
     * @param \Vnecoms\VendorsCredit\Model\Escrow $escrow
     */
    public function sendEscrowNotificationEmail(\Vnecoms\VendorsCredit\Model\Escrow $escrow)
    {
        $vendor = $escrow->getVendor();
        if (!$vendor->getId()) {
            return;
        }
        if (!$escrow->getId()) {
            return;
        }
    
        /*Send notification emails here*/
    
        switch ($escrow->getStatus()) {
            case Escrow::STATUS_PENDING:
                $template = self::XML_PATH_NEW_ESCROW_TEMPLATE;
                break;
            case Escrow::STATUS_COMPLETED:
                $template = self::XML_PATH_ESCROW_RELEASED_TEMPLATE;
                break;
            case Escrow::STATUS_CANCELED:
                $template = self::XML_PATH_ESCROW_CANCELED_TEMPLATE;
                break;
        }
    
        $vendorInvoice = $escrow->getVendorInvoice();
        $vendorOrder = $vendorInvoice->getVendorOrder();

        $baseCurrency = $this->_storeManager->getStore()->getBaseCurrency();
        $escrow->setData('amount_formated', $baseCurrency->formatPrecision($escrow->getAmount(), 2, [], false));
        $escrow->setData('created_at_formated', $this->_localeDate->formatDateTime(
            $escrow->getCreatedAt(),
            \IntlDateFormatter::MEDIUM,
            \IntlDateFormatter::MEDIUM
        ));
        

        try {
            $this->_emailHelper->sendTransactionEmail(
                $template,
                \Magento\Framework\App\Area::AREA_FRONTEND,
                self::XML_PATH_ESCROW_EMAIL_SENDER,
                $vendor->getEmail(),
                [
                  'vendor' => $vendor,
                  'escrow' => $escrow,
                  'vendor_order' => $vendorOrder,
                  'vendor_invoice' => $vendorInvoice,
                  'order' => $vendorOrder->getOrder(),
                  'invoice' => $vendorInvoice->getInvoice(),
                ]
            );
        } catch (\Exception $e) {
        }
    }
    
    /**
     * Format base currency for withdrawal object
     *
     * @param \Vnecoms\VendorsCredit\Model\Withdrawal $withdrawal
     */
    protected function formatWithdrawalData(
        \Vnecoms\VendorsCredit\Model\Withdrawal $withdrawal
    ) {
        $baseCurrency = $this->_storeManager->getStore()->getBaseCurrency();
        $withdrawal->setData(
            'amount_formated',
            $baseCurrency->formatPrecision($withdrawal->getAmount(), 2, [], false)
        )->setData(
            'fee_formated',
            $baseCurrency->formatPrecision($withdrawal->getFee(), 2, [], false)
        )->setData(
            'net_amount_formated',
            $baseCurrency->formatPrecision($withdrawal->getNetAmount(), 2, [], false)
        )->setData(
            'created_at_formated',
            $this->_localeDate->formatDateTime(
                $withdrawal->getCreatedAt(),
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::MEDIUM
            )
        );
    }
    
    /**
     * Get withdrawal methods
     * 
     * @return array
     */
    public function getWithdrawalMethods(){
        $withdawalMethods = [];
        foreach($this->scopeConfig->getValue('withdrawal_methods') as $code => $config){
            if(!$config['model']) throw new \Exception(__("Model is not defined in the withdrawal method %1",$code));
            if(!isset($config['active']) || !$config['active']) continue;
            $withdawalMethods[$code] = $this->objectManager->create($config['model']);
        }
        return $withdawalMethods;
    }
    
    /**
     * Get Credit Hold Time Days
     *
     * @return int
     */
    public function getHoldTimeDays()
    {
        return $this->scopeConfig->getValue('vendors/credit/hold_time');
    }
}

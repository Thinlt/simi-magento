<?php
namespace Vnecoms\VendorsCredit\Model;

use Vnecoms\VendorsCredit\Model\CreditProcessor\OrderPayment;
use Vnecoms\VendorsCredit\Model\CreditProcessor\ItemCommission;

/**
 * @method string getVendorId();
 * @method string getRelationId();
 * @method string getAmount();
 * @method string getStatus();
 * @method string getCreatedAt();
 * @method string getUpdatedAt();
 */

class Escrow extends \Magento\Framework\Model\AbstractModel
{

    const ENTITY = 'vendor_escrow';
    
    const STATUS_CANCELED = 0;
    const STATUS_PENDING = 1;
    const STATUS_COMPLETED = 2;
    
    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'vendor_escrow';
    
    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'vendor_escrow';

    /**
     * Vendor Object
     *
     * @var \Vnecoms\Vendors\Model\Vendor
     */
    protected $_vendor;
    
    /**
     * Vendor Invoice
     *
     * @var \Vnecoms\VendorsSales\Model\Order\Invoice
     */
    protected $_invoice;
    
    /**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Vnecoms\VendorsCredit\Model\ResourceModel\Escrow');
    }
    
    /**
     * Send escrow notification email to vendor.
     *
     * @see \Magento\Framework\Model\AbstractModel::afterSave()
     */
    public function afterSave()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $creditHelper = $om->create('Vnecoms\VendorsCredit\Helper\Data');
        $creditHelper->sendEscrowNotificationEmail($this);
        return parent::afterSave();
    }
    
    /**
     * Can cancel
     *
     * @return boolean
     */
    public function canCancel()
    {
        return $this->getStatus() == self::STATUS_PENDING;
    }
    
    /**
     * Cancel The Withdrawal
     *
     * @throws \Exception
     * @return \Vnecoms\VendorsCredit\Model\Withdrawal
     */
    public function cancel()
    {
        if (!$this->canCancel()) {
            throw new \Exception(__("Can not cancel this transaction."));
        }
        
        $this->setStatus(self::STATUS_CANCELED)->save();
        return $this;
    }
    
    /**
     * Can release the request
     *
     * @return boolean
     */
    public function canRelease()
    {
        return $this->canCancel();
    }
    
    /**
     * Release the pending credit
     *
     * @throws \Exception
     * @return \Vnecoms\VendorsCredit\Model\Escrow
     */
    public function release()
    {
        if (!$this->canRelease()) {
            throw new \Exception(__("Can not release this transaction."));
        }
        
        /*Process pending payment.*/
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $vendor = $this->getVendor();
        
        $creditAccount = $om->create('Vnecoms\Credit\Model\Credit');
        $creditAccount->loadByCustomerId($vendor->getCustomer()->getId());
        
        $creditProcessor = $om->create('Vnecoms\Credit\Model\Processor');
        
        /** @var \Vnecoms\VendorsSales\Model\Order\Invoice */
        $vendorInvoice = $this->getInvoice();
        
        /** @var \Vnecoms\VendorsSales\Model\Order */
        $vendorOrder = $vendorInvoice->getVendorOrder();
        
        /** @var \Magento\Sales\Model\Order */
        $order = $vendorOrder->getOrder();
        
        $amount = $this->getAmount();
        
        /*Create transaction to add invoice grandtotal to vendor credit account.*/
        $data = [
            'vendor' => $vendor,
            'type' => OrderPayment::TYPE,
            'amount' => $amount,
            'vendor_order' => $vendorOrder,
            'vendor_invoice' => $vendorInvoice,
            'order' => $order
        ];
        
        $creditProcessor->process($creditAccount, $data);
        
        /*Calculate commission and create transaction for each item.*/
        foreach ($vendorInvoice->getAllItems() as $item) {
            $orderItem  = $item->getOrderItem();
            if ($orderItem->getParentItemId()) {
                continue;
            }
            $product = $om->create('Magento\Catalog\Model\Product')->load($orderItem->getProductId());
            $trans = $om->create('Vnecoms\Credit\Model\Credit\Transaction')->getCollection()
                ->addFieldToFilter('type', ItemCommission::TYPE)
                ->addFieldToFilter('additional_info', ['like'=>'invoice_item|'.$item->getId().'%']);
            if ($trans->count()) {
                continue;
            }
        
            $amount = $item->getBaseRowTotal();
            $fee = $item->getCommission();
        
            /*Do nothing if the fee is zero*/
            if ($fee <= 0) {
                continue;
            }
        
            $additionalDescription = $item->getCommissionDescription();
        
            $data = [
                'vendor' => $vendor,
                'type' => ItemCommission::TYPE,
                'amount' => $fee,
                'invoice_item' => $item,
                'order' => $order,
                'vendor_invoice' => $vendorInvoice,
                'additional_description' => $additionalDescription,
            ];
        
            $creditProcessor->process($creditAccount, $data);
        }
        
        $this->setStatus(self::STATUS_COMPLETED)->save();
        return $this;
    }
    
    /**
     * Get Vendor
     *
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function getVendor()
    {
        if (!$this->_vendor) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_vendor = $om->create('Vnecoms\Vendors\Model\Vendor');
            $this->_vendor->load($this->getVendorId());
        }
        
        return $this->_vendor;
    }
    /**
     * Get Invoice Id
     *
     * @return string
     */
    public function getInvoiceId()
    {
        return $this->getRelationId();
    }
    
    /**
     * Get Vendor Invoice
     *
     * @return \Vnecoms\VendorsSales\Model\Invoice
     */
    
    public function getVendorInvoice()
    {
        return $this->getInvoice();
    }
    
    /**
     * Get Vendor Invoice
     *
     * @return \Vnecoms\VendorsSales\Model\Order\Invoice
     */
    public function getInvoice()
    {
        if (!$this->_invoice) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_invoice = $om->create('Vnecoms\VendorsSales\Model\Order\Invoice');
            $this->_invoice->load($this->getRelationId());
        }
        
        return $this->_invoice;
    }
}

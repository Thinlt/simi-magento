<?php
namespace Vnecoms\VendorsCredit\Block\Form\Element;

use Magento\Framework\UrlInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Escaper;
use Vnecoms\VendorsSales\Model\Order\InvoiceFactory;

class Order extends AbstractElement
{

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @var InvoiceFactory
     */
    protected $_invoiceFactory;
    
    /**
     * Constructor
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param \Vnecoms\VendorsCredit\Model\Source\Status $withdrawalStatus
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        UrlInterface $urlBuilder,
        InvoiceFactory $invoiceFactory,
        $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->_invoiceFactory = $invoiceFactory;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }
    
    /**
     * Get Element HTML
     * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
     */
    public function getElementHtml()
    {
        $invoiceId = $this->getValue();
        $vendorInvoice = $this->_invoiceFactory->create();
        $vendorInvoice->load($invoiceId);

        $order = $vendorInvoice->getInvoice()->getOrder();
        
        $url = $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $order->getId()]);
        ;
        return '<a href="'.$url.'">'.$order->getIncrementId().'</a>';
    }
}

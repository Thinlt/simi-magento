<?php

namespace Vnecoms\VendorsPdf\Block\Vendors\Order;

class PrintButton extends \Magento\Backend\Block\Widget\Container
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    protected $helper;
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Vnecoms\PdfPro\Helper\Data $helper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        if ($this->helper->isEnableModule()) {
            $this->addButton(
                'pdfpro_print',
                [
                    'label' => __('Print'),
                    'class' => 'fa fa-print',
                    'onclick' => 'setLocation(\''.$this->getPdfPrintUrl().'\')',
                ]
            );
        }
        parent::_construct();
    }

    /**
     * @return string
     */
    public function getPdfPrintUrl()
    {
        return $this->getUrl('sales/order/print',['order_id' => $this->getOrderId()]);
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->coreRegistry->registry('vendor_order')->getId();
    }
}

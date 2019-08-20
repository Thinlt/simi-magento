<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Sales\Order;

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

    public function isLoadedLib()
    {
        return true;
        if (is_dir($this->helper->getPdfLibDir()) and file_exists($this->helper->getPdfLibDir().'/vendor/autoload.php')) {
            return true;
        }

        return false;
    }

    protected function _construct()
    {
        if (!$this->isLoadedLib()) {
            return;
        }
        if ($this->_scopeConfig->isSetFlag('pdfpro/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    && $this->_scopeConfig->isSetFlag('pdfpro/general/admin_print_order', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $this->addButton(
                'pdfpro_print',
                [
                    'label' => 'Easy PDF - '.__('Print Order'),
                    'class' => 'print',
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
        return $this->getUrl('vnecoms_pdfpro/pdfpro_order/print',['order_id' => $this->getOrderId()]);
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->coreRegistry->registry('sales_order')->getId();
    }
}

<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\Sales\Order\Creditmemo;

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
        $this->helper = $helper;
        $this->coreRegistry = $registry;
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
        if (!$this->helper->isEnableModule()) {
            return;
        }
        if (!$this->isLoadedLib()) {
            return;
        }
        if ($this->helper->getConfig('pdfpro/general/remove_default_print')) {
            $this->removeButton('print');
        }
        if ($this->helper->getConfig('pdfpro/general/admin_print_order')) {
            $this->addButton(
                'pdfpro_print',
                [
                    'label' => 'Easy PDF - '.__('Print Creditmemo'),
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
        return $this->getUrl('vnecoms_pdfpro/pdfpro_creditmemo/print',['creditmemo_id' => $this->getRequest()->getParam('creditmemo_id')]);
    }
}

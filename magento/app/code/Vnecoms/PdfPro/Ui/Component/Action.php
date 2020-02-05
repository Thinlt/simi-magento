<?php

namespace Vnecoms\PdfPro\Ui\Component;

use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Action.
 */
class Action extends \Magento\Ui\Component\Action
{
    protected $_helper;

    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        ContextInterface $context, array $components = [], array $data = [], $actions = null
    ) {
        $this->_helper = $helper;
        if (!$this->_helper->getConfig('pdfpro/general/enabled') || !$this->_helper->getConfig('pdfpro/general/admin_print_order')) {
            $data = [];
        }

        parent::__construct($context, $components, $data, $actions);
    }
}

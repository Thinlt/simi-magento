<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\System\Config\Form\Fieldset;

class Author extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    protected $_helper;
    /**
     * @param \Magento\Backend\Block\Context      $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\View\Helper\Js   $jsHelper
     * @param array                               $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Helper\Js $jsHelper,
        \Vnecoms\PdfPro\Helper\Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $authSession, $jsHelper, $data);
    }
    /**
     * Return header comment part of html for fieldset.
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        $html = '<div style="margin:5px 0px;padding:3px;">';
        $html .= '
            <p>The PDF Pro for Magento 2 is developed and supported by <a href="https://www.vnecoms.com" target="_blank">www.VnEcoms.com</a>.</p>
            <p>If you need any support or have any question please contact us at <a href="mailto:support@vnecoms.com">support@vnecoms.com</a> or submit a ticket at <a href="https://www.vnecoms.com/contacts/" target="_blank">https://www.vnecoms.com/contacts/</a></p>
        ';
        $html .= '</div>';

        return $html;
    }
}

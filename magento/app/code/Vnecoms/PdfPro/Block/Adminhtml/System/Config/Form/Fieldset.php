<?php

namespace Vnecoms\PdfPro\Block\Adminhtml\System\Config\Form;

class Fieldset extends \Magento\Config\Block\System\Config\Form\Fieldset
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

        $info = array('easypdfinvoice' => array('label' => __('PDF Invoice Pro Version'), 'value' => $this->_helper->getVersion()));

        $html = '
        <div style="margin-bottom: 20px; display: block; padding: 5px; position: relative;">
        <table class="form-list" cellspacing="0">
        ';
        $transport = new \Magento\Framework\DataObject($info);
        $info = $transport->getData();

        foreach ($info as $row) {
            $html .= '<tr><td class="label">'.$row['label'].'</td><td class="value"><strong style="color: #1f5e00;">'.$row['value'].'</strong></td></tr>';
        }

        $html .= '
        </table>
        </div>';

        return $html.$element->getComment();
    }
}

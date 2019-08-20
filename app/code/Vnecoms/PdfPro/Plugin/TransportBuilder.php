<?php
/**
 * Created by PhpStorm.
 * User: TUNA
 * Date: 4/4/2019
 * Time: 10:53 PM
 */

namespace Vnecoms\PdfPro\Plugin;


class TransportBuilder
{
    /**
     * @var \Vnecoms\PdfPro\Model\NextEmailInfo
     */
    private $nextEmail;

    public function __construct(
        \Vnecoms\PdfPro\Model\NextEmailInfo $nextEmailInfo
    ) {
        $this->nextEmail = $nextEmailInfo;
    }

    /**
     * @param \Magento\Framework\Mail\Template\TransportBuilder $subject
     * @param $templateIdentifier
     * @return void
     */
    public function beforeSetTemplateIdentifier(
        \Magento\Framework\Mail\Template\TransportBuilder $subject,
        $templateIdentifier
    ) {
        $this->nextEmail->setTemplateIdentifier($templateIdentifier);
    }

    /**
     * @param \Magento\Framework\Mail\Template\TransportBuilder $subject
     * @param $templateVars
     * @return void
     */
    public function beforeSetTemplateVars(
        \Magento\Framework\Mail\Template\TransportBuilder $subject,
        $templateVars
    ) {
        $this->nextEmail->setTemplateVars($templateVars);
    }

    public function aroundGetTransport(
        \Magento\Framework\Mail\Template\TransportBuilder $subject,
        \Closure $proceed
    ) {
        $mailTransport = $proceed();
        $this->reset();
        return $mailTransport;
    }

    /**
     * @return void
     */
    private function reset()
    {
        $this->nextEmail->setTemplateIdentifier(null);
        $this->nextEmail->setTemplateVars(null);
    }
}
<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsSales\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Email
{
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }
    
    /**
     * Send transaction email
     * @param string $templateIdentifier
     * @param string $area
     * @param string $fromEmailIdentifier
     * @param string $toEmail
     * @param string $replyTo
     * @param int $storeId
     * @param array $templateVars
     * @param string $scope
     */
    public function sendTransactionEmail(
        $templateIdentifier,
        $area,
        $fromEmailIdentifier,
        $toEmail,
        $templateVars = [],
        $replyTo = '',
        $copyVars = [],
        $storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID,
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT
    ) {
        $this->inlineTranslation->suspend();
        $transport = $this->_transportBuilder
        ->setTemplateIdentifier($this->scopeConfig->getValue($templateIdentifier, $scope))
        ->setTemplateOptions(
            [
                'area' => $area,
                'store' => $storeId,
            ]
        )
        ->setTemplateVars($templateVars)
        ->setFrom($this->scopeConfig->getValue($fromEmailIdentifier, $scope))
        ->addTo($toEmail);

        if (count($copyVars)) {
            foreach ($copyVars as $method => $emaillist) {
                $emails = explode(",", $emaillist);
                foreach ($emails as $email) {
                    if ($method == 'bcc') {
                        $transport->addBcc($email);
                    } else {
                        $transport->addCc($email);
                    }
                }
            }
        }

        $transport->getTransport();

        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
}

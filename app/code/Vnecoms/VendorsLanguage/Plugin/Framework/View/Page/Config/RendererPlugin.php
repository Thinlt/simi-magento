<?php
/**
 * Copyright © 2018 Vnecoms. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Vnecoms\VendorsLanguage\Plugin\Framework\View\Page\Config;

use \Magento\Framework\View\Page\Config as PageConfig;
use \Vnecoms\Vendors\App\Area\FrontNameResolver;

/**
 * Class with class map capability
 *
 * ...
 */
class RendererPlugin
{
    /**
     * @var PageConfig
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Vnecoms\VendorsConfig\Helper\Data
     */
    protected $vendorConfig;

    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $vendorsSession;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    public function __construct(
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        PageConfig $pageConfig,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\State $appState,
        \Vnecoms\VendorsConfig\Helper\Data $vendorConfig,
        \Vnecoms\Vendors\Model\Session $vendorSession
    ) {
        $this->localeResolver = $localeResolver;
        $this->pageConfig = $pageConfig;
        $this->moduleManager = $moduleManager;
        $this->vendorConfig = $vendorConfig;
        $this->vendorsSession = $vendorSession;
        $this->appState = $appState;
    }

    /**
     * Interceptors around render element page
     *
     * @param PageConfig\Renderer $subject
     * @param \Closure $proceed
     * @param $elementType
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundRenderElementAttributes(
        \Magento\Framework\View\Page\Config\Renderer $subject,
        \Closure $proceed,
        $elementType
    ) {
        $areaCode = $this->appState->getAreaCode();
		$vendorLocaleCode = $this->vendorConfig->getVendorConfig('general/locale/code', $this->vendorsSession->getVendor()->getId());
        if (!$this->moduleManager->isEnabled('Vnecoms_Vendors')
			|| !$this->vendorsSession->getVendor()->getId()
			|| !$this->moduleManager->isEnabled('Vnecoms_VendorsLanguage')
			|| $areaCode !== FrontNameResolver::AREA_CODE
			|| empty($vendorLocaleCode)
        ) {
            return $proceed($elementType);
        }

        $resultAttributes = [];
        foreach ($this->pageConfig->getElementAttributes($elementType) as $name => $value) {
            if ($elementType == PageConfig::ELEMENT_TYPE_HTML && $name == PageConfig::HTML_ATTRIBUTE_LANG) {
                $value = strstr($vendorLocaleCode, '_', true);
                $name == PageConfig::HTML_ATTRIBUTE_LANG;
            }
            $resultAttributes[] = sprintf('%s="%s"', $name, $value);
        }
        return implode(' ', $resultAttributes);
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Ui\Plugin\Component\Form\Element\DataType;

use Magento\Ui\Component\Form\Element\DataType\Date as BaseDate;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Timezone library
 */
class DatePlugin
{

    protected $locale;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Vnecoms\Vendors\Model\Session
     */
    protected $vendorsSession;


    public function __construct(
        ContextInterface $context,
        TimezoneInterface $localeDate,
        ResolverInterface $localeResolver,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\RequestInterface $request,
        \Vnecoms\Vendors\Model\Session $vendorsSession
    ) {
        $this->locale = $localeResolver->getLocale();
        $this->localeDate = $localeDate;
        $this->moduleManager = $moduleManager;
        $this->vendorsSession = $vendorsSession;
        $this->request = $request;
    }

    /**
     * Interceptor After Prepare Method
     *
     * @param BaseDate $subject
     * @return void
     */
    public function afterPrepare(
        \Magento\Ui\Component\Form\Element\DataType\Date $subject
    ) {
        if (
            !$this->moduleManager->isEnabled('Vnecoms_Vendors') ||
            !$this->vendorsSession->getVendor()->getId()
        ) {
            return; // Do nothing
        }

        // Set date format pattern by current locale
        $config = $subject->getData('config');
        if ($this->locale == 'ar_SA') {
            $localeDateFormat = "MM/dd/y";
        } else {
            $localeDateFormat = $this->localeDate->getDateFormat();
        }

        $config['options']['dateFormat'] = $localeDateFormat;
        $subject->setData('config', $config);
    }
}

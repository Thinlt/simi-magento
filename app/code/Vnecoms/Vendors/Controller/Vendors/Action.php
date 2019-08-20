<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Controller\Vendors;

use Vnecoms\Vendors\App\AbstractAction;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

/**
 * Index backend controller
 */
abstract class Action extends AbstractAction
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * Date filter instance
     *
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @var \Vnecoms\Vendors\App\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;


    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $context->getCoreRegsitry();
        $this->_dateFilter = $context->getDateFilter();
        $this->_config = $context->getConfig();
        $this->_localeResolver = $context->getLocaleResolver();
    }

    /**
     * Init action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();

        $config = $this->_view->getPage()->getConfig();
        $config->setElementAttribute(
            \Magento\Framework\View\Page\Config::ELEMENT_TYPE_HTML,
            \Magento\Framework\View\Page\Config::HTML_ATTRIBUTE_LANG,
            strstr($this->_localeResolver->getLocale(), '_', true)
        );

        $config->getTitle()->set($this->_config->getValue(self::XML_PATH_VENDOR_DESIGN_HEAD_DEFAULT_TITLE));
        return $this;
    }
}

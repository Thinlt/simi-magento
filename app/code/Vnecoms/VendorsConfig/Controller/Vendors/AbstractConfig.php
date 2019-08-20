<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\VendorsConfig\Controller\Vendors;

use Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker;

/**
 * System Configuration Abstract Controller
 */
abstract class AbstractConfig extends \Vnecoms\Vendors\App\AbstractAction
{
    /**
     * @var \Magento\Config\Model\Config\Structure
     */
    protected $_configStructure;

    /**
     * @var ConfigSectionChecker
     */
    protected $_sectionChecker;

    /**
     * @param \Vnecoms\Vendors\App\Action\Context $context
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param ConfigSectionChecker $sectionChecker
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context $context,
        \Magento\Config\Model\Config\Structure $configStructure,
        ConfigSectionChecker $sectionChecker
    ) {
        parent::__construct($context);
        $this->_configStructure = $configStructure;
        $this->_sectionChecker = $sectionChecker;
    }

    /**
     * Check if current section is found and is allowed
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$request->getParam('section')) {
            $request->setParam('section', $this->_configStructure->getFirstSection()->getId());
        }
        return parent::dispatch($request);
    }
}

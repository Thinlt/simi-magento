<?php

namespace Simi\Simistorelocator\Block;

class Link extends \Magento\Framework\View\Element\Html\Link {

    /**
     * @var \Simi\Simistorelocator\Model\SystemConfig
     */
    public $systemConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Url                      $customerUrl
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Simi\Simistorelocator\Model\SystemConfig $systemConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->systemConfig = $systemConfig;
    }

    /**
     * Render block HTML.
     *
     * @return string
     */
    protected function _toHtml() {
        $check = ($this->systemConfig->isEnableFrontend()
                && $this->systemConfig->isShowTopLink());

        return $check ? parent::_toHtml() : '';
    }

    /**
     * @return string
     */
    public function getHref() {
        return $this->getUrl('simistorelocator/index/index');
    }

}

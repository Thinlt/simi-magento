<?php

namespace Simi\Simistorelocator\Block;

class AbstractBlock extends \Magento\Framework\View\Element\Template {

    /**
     * @var \Simi\Simistorelocator\Model\SystemConfig
     */
    public $systemConfig;

    /**
     * @var \Simi\Simistorelocator\Helper\Image
     */
    public $imageHelper;

    /**
     * @var \Simi\Simistorelocator\Model\ResourceModel\Store\CollectionFactory
     */
    public $storeCollectionFactory;

    /**
     * @var \Simi\Simistorelocator\Model\ResourceModel\Tag\CollectionFactory
     */
    public $tagCollectionFactory;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Url                      $customerUrl
     * @param array                                            $data
     */
    public function __construct(
        \Simi\Simistorelocator\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->systemConfig = $context->getSystemConfig();
        $this->imageHelper = $context->getImageHelper();
        $this->storeCollectionFactory = $context->getStoreCollectionFactory();
        $this->tagCollectionFactory = $context->getTagCollectionFactory();
        $this->coreRegistry = $context->getCoreRegistry();
    }

    /**
     * @return \Simi\Simistorelocator\Model\SystemConfig
     */
    public function getSystemConfig() {
        return $this->systemConfig;
    }

    /**
     * Render block HTML.
     *
     * @return string
     */
    protected function _toHtml() {
        return $this->_systemConfig->isEnableFrontend() ? parent::_toHtml() : '';
    }

    /**
     * @return \Simi\Simistorelocator\Model\ResourceModel\Store\Collection
     */
    public function getStoreCollection() {
        return $this->storeCollectionFactory->create();
    }

    /**
     * @return \Simi\Simistorelocator\Model\ResourceModel\Tag\Collection
     */
    public function getTagCollection() {
        return $this->tagCollectionFactory->create();
    }

    public function getMediaUrlImage($imagePath = '') {
        return $this->imageHelper->getMediaUrlImage($imagePath);
    }

}

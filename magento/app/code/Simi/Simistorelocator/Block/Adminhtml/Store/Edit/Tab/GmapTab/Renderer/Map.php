<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Store\Edit\Tab\GmapTab\Renderer;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\UrlInterface;

class Map extends \Magento\Backend\Block\Widget implements RendererInterface {

    protected $_template = 'Simi_Simistorelocator::store/map.phtml';

    /**
     * @var array
     */
    public $locationInputIds = [
        'address',
        'zoom_level',
        'city',
        'zipcode',
        'country_id',
        'latitude',
        'longitude',
        'zoom_level',
    ];

    /**
     * @var array
     */
    public $jsonKeys = [
        'latitude',
        'longitude',
        'zoom_level',
        'marker_icon',
    ];

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Simi\Simistorelocator\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Simi\Simistorelocator\Model\SystemConfig $systemConfig,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_systemConfig = $systemConfig;
        $this->_coreRegistry = $registry;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getGoolgeApiKey($store = null) {
        return $this->_systemConfig->getGoolgeApiKey();
    }

    /**
     * Render form element as HTML.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $this->setElement($element);

        return $this->toHtml();
    }

    /**
     * @return \Simi\Simistorelocator\Model\Store
     */
    public function getRegistryModel() {
        return $this->_coreRegistry->registry('simistorelocator_store');
    }

    /**
     * @return mixed
     */
    public function getHtmlIdPrefix() {
        return $this->getElement()->getForm()->getHtmlIdPrefix();
    }

    /**
     * @param string $elementId
     *
     * @return string
     */
    public function getSelectorElement($elementId = '') {
        return '#' . $this->getHtmlIdPrefix() . $elementId;
    }

    /**
     * @return string
     */
    public function getOptionMapJson() {
        $store = $this->getRegistryModel();

        if ($store->getData('marker_icon')) {
            $markerIcon = $this->_storeManager->getStore()
                            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $store->getData('marker_icon');
            $store->setData('marker_icon', $markerIcon);
        }

        foreach ($this->locationInputIds as $input) {
            $store->setData('input_' . $input, $this->getSelectorElement($input));
            $this->jsonKeys[] = 'input_' . $input;
        }

        return $store->toJson($this->jsonKeys);
    }
}

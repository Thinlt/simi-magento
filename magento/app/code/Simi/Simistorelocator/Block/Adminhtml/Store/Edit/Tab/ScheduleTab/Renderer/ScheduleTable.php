<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Store\Edit\Tab\ScheduleTab\Renderer;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class ScheduleTable extends \Magento\Backend\Block\Widget implements RendererInterface {

    protected $_template = 'Simi_Simistorelocator::store/scheduletable.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * Model Url instance.
     *
     * @var \Magento\Backend\Model\UrlInterface
     */
    public $backendUrl;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\UrlFactory $backendUrlFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $registry;
        $this->backendUrl = $backendUrlFactory->create();
    }

    /**
     * Preparing global layout.
     *
     * @return $this
     */
    protected function _prepareLayout() {
        $tableGrid = $this->getLayout()
                ->createBlock('Simi\Simistorelocator\Block\Adminhtml\Store\Edit\Tab\ScheduleTab\TableGrid');

        /** @var \Simi\Simistorelocator\Model\Store $store */
        $store = $this->getRegistryModel();
        $tableGrid->setData('schedule_id', $store->getScheduleId());
        $this->setChild('schedule_table_grid', $tableGrid);

        return parent::_prepareLayout();
    }

    /**
     * get registry model.
     *
     * @return \Simi\Simistorelocator\Model\Store
     */
    public function getRegistryModel() {
        return $this->coreRegistry->registry('simistorelocator_store');
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
    public function getRegistyStore() {
        return $this->coreRegistry->registry('simistorelocator_store');
    }

    /**
     * get url to load schedule table grid by ajax.
     *
     * @return string
     */
    public function getAjaxLoadScheduleUrl() {
        return $this->backendUrl->getUrl('simistorelocatoradmin/store/scheduletable');
    }
}

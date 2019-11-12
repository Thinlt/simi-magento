<?php

namespace Simi\Simistorelocator\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    /**
     * @var \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter
     */
    public $converter;

    /**
     * @var \Simi\Simistorelocator\Model\Factory
     */
    public $factory;

    /**
     * @var \Simi\Simistorelocator\Model\StoreFactory
     */
    public $storeFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    public $backendSession;

    /**
     * @var array
     */
    public $sessionData = null;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    public $backendHelperJs;

    /**
     * Block constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Simi\Simistorelocator\Model\Factory $factory,
        \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter,
        \Magento\Backend\Helper\Js $backendHelperJs,
        \Magento\Backend\Model\Session $backendSession,
        \Simi\Simistorelocator\Model\StoreFactory $storeFactory
    ) {
        parent::__construct($context);
        $this->factory = $factory;
        $this->converter = $converter;
        $this->storeFactory = $storeFactory;
        $this->backendHelperJs = $backendHelperJs;
        $this->backendSession = $backendSession;
    }

    /**
     * get selected stores in serilaze grid store.
     *
     * @return array
     */
    public function getTreeSelectedStores() {
        $sessionData = $this->_getSessionData();

        if ($sessionData) {
            return $this->converter->toTreeArray(
                            $this->backendHelperJs->decodeGridSerializedInput($sessionData)
            );
        }

        $entityType = $this->_getRequest()->getParam('entity_type');
        $id = $this->_getRequest()->getParam('enitity_id');

        /** @var \Simi\Simistorelocator\Model\AbstractModelManageStores $model */
        $model = $this->factory->create($entityType)->load($id);

        return $model->getId() ? $this->converter->toTreeArray($model->getStorelocatorIds()) : [];
    }

    /**
     * get selected rows in serilaze grid of tag, holiday, specialday.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTreeSelectedValues() {
        $sessionData = $this->_getSessionData();

        if ($sessionData) {
            return $this->converter->toTreeArray(
                            $this->backendHelperJs->decodeGridSerializedInput($sessionData)
            );
        }

        $storelocatorId = $this->_getRequest()->getParam('simistorelocator_id');
        $methodGetterId = $this->_getRequest()->getParam('method_getter_id');

        /** @var \Simi\Simistorelocator\Model\Store $store */
        $store = $this->storeFactory->create()->load($storelocatorId);
        $ids = $store->runGetterMethod($methodGetterId);

        return $store->getId() ? $this->converter->toTreeArray($ids) : [];
    }

    /**
     * Get session data.
     *
     * @return array
     */
    protected function _getSessionData() {
        $serializedName = $this->_getRequest()->getParam('serialized_name');
        if ($this->sessionData === null) {
            $this->sessionData = $this->backendSession->getData($serializedName, true);
        }

        return $this->sessionData;
    }

}

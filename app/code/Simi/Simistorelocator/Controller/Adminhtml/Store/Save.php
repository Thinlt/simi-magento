<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Store;

class Save extends \Simi\Simistorelocator\Controller\Adminhtml\Store {

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute() {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            $id = $this->getRequest()->getParam(static::PARAM_CRUD_ID);

            /** @var \Simi\Simistorelocator\Model\Store $model */
            $model = $this->_createMainModel()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This Store no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            $this->_prepareSerializedData($model);

            try {
                $this->imageHelper->mediaUploadImage(
                        $model, 'marker_icon', \Simi\Simistorelocator\Model\Store::MARKER_ICON_RELATIVE_PATH, $makeResize = false
                );

                $model->save();
                $this->updateVisibility($this->_objectManager,$model,$data);
                $this->messageManager->addSuccess(__('The Store has been saved.'));
                $this->_getSession()->setFormData(false);

                return $this->_getBackResultRedirect($resultRedirect, $model->getId());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while saving the Store.'));
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setFormData($data);

                $this->_getSession()->setSerializedTags($model->getData('serialized_tags'));
                $this->_getSession()->setSerializedHolidays($model->getData('serialized_holidays'));
                $this->_getSession()->setSerializedSpecialdays($model->getData('serialized_specialdays'));

                return $resultRedirect->setPath('*/*/edit', [static::PARAM_CRUD_ID => $id]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Prepare serialized data for model.
     *
     * @param \Simi\Simistorelocator\Model\Store $model
     *
     * @return $this
     */
    protected function _prepareSerializedData(\Simi\Simistorelocator\Model\Store $model) {
        if ($model->hasData('serialized_tags')) {
            $model->setData(
                    'in_tag_ids', $this->backendHelperJs->decodeGridSerializedInput($model->getData('serialized_tags'))
            );
        }

        if ($model->hasData('serialized_holidays')) {
            $model->setData(
                    'in_holiday_ids', $this->backendHelperJs->decodeGridSerializedInput($model->getData('serialized_holidays'))
            );
        }

        if ($model->hasData('serialized_specialdays')) {
            $model->setData(
                    'in_specialday_ids', $this->backendHelperJs->decodeGridSerializedInput($model->getData('serialized_specialdays'))
            );
        }

        return $this;
    }

    private function updateVisibility($simiObjectManager, $model, $data)
    {
        $simiconnectorhelper = $simiObjectManager->get('Simi\Simiconnector\Helper\Data');
        if ($data['storeview_id'] && is_array($data['storeview_id'])) {
            $typeID            = $simiconnectorhelper->getVisibilityTypeId('storelocator');
            $visibleStoreViews = $simiObjectManager
                ->create('Simi\Simiconnector\Model\Visibility')->getCollection()
                ->addFieldToFilter('content_type', $typeID)
                ->addFieldToFilter('item_id', $model->getId());
            $visibleStoreViews->walk('delete');
            foreach ($visibleStoreViews as $visibilityItem) {
                $simiObjectManager
                    ->get('Simi\Simiconnector\Helper\Data')->deleteModel($visibilityItem);
            }
            foreach ($data['storeview_id'] as $storeViewId) {
                $visibilityItem = $simiObjectManager->create('Simi\Simiconnector\Model\Visibility');
                $visibilityItem->setData('content_type', $typeID);
                $visibilityItem->setData('item_id', $model->getId());
                $visibilityItem->setData('store_view_id', $storeViewId);
                $simiObjectManager
                    ->get('Simi\Simiconnector\Helper\Data')->saveModel($visibilityItem);
            }
        }
    }

}

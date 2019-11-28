<?php

namespace Simi\Simicustomize\Controller\Adminhtml\Newcollections;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $simiObjectManager = $this->_objectManager;
        $model = $simiObjectManager->create('Simi\Simicustomize\Model\Newcollections');

        $id = $this->getRequest()->getParam('newcollections_id');
        if ($id) {
            $model->load($id);
        }

        // if (isset($data['category_id_0'])) {
        //     $cat0 = $simiObjectManager->create('Magento\Catalog\Model\Category')->load($data['category_id_0']);
        // }

        $is_delete_newcollections_0      = isset($data['newcollections_filename_0']['delete']) ?
        $data['newcollections_filename_0']['delete'] : false;
        $data['newcollections_filename_0'] = isset($data['newcollections_filename_0']['value']) ?
        $data['newcollections_filename_0']['value'] : '';

        $is_delete_newcollections_1      = isset($data['newcollections_filename_1']['delete']) ?
        $data['newcollections_filename_1']['delete'] : false;
        $data['newcollections_filename_1'] = isset($data['newcollections_filename_1']['value']) ?
        $data['newcollections_filename_1']['value'] : '';

        $is_delete_newcollections_2      = isset($data['newcollections_filename_2']['delete']) ?
        $data['newcollections_filename_2']['delete'] : false;
        $data['newcollections_filename_2'] = isset($data['newcollections_filename_2']['value']) ?
        $data['newcollections_filename_2']['value'] : '';

        $is_delete_newcollections_3      = isset($data['newcollections_filename_3']['delete']) ?
        $data['newcollections_filename_3']['delete'] : false;
        $data['newcollections_filename_3'] = isset($data['newcollections_filename_3']['value']) ?
        $data['newcollections_filename_3']['value'] : '';

        // $is_delete_newcollections_tablet        = isset($data['newcollections_filename_tablet']['delete']) ?
        // $data['newcollections_filename_tablet']['delete'] : false;
        // $data['newcollections_filename_tablet'] = isset($data['newcollections_filename_tablet']['value']) ?
        // $data['newcollections_filename_tablet']['value'] : '';

        $model->addData($data);

        try {
            $imageHelper = $simiObjectManager->get('Simi\Simiconnector\Helper\Data');

            if ($is_delete_newcollections_0 && $model->getData('newcollections_filename_0')) {
                $model->setData('newcollections_filename_0', '');
            } else {
                $imageFile = $imageHelper->uploadImage('newcollections_filename_0', 'newcollections_0');
                if ($imageFile) {
                    $model->setData('newcollections_filename_0', $imageFile);
                }
            }

            if ($is_delete_newcollections_0 && $model->getData('newcollections_filename_1')) {
                $model->setData('newcollections_filename_1', '');
            } else {
                $imageFile = $imageHelper->uploadImage('newcollections_filename_1', 'newcollections_1');
                if ($imageFile) {
                    $model->setData('newcollections_filename_1', $imageFile);
                }
            }

            if ($is_delete_newcollections_0 && $model->getData('newcollections_filename_2')) {
                $model->setData('newcollections_filename_2', '');
            } else {
                $imageFile = $imageHelper->uploadImage('newcollections_filename_2', 'newcollections_2');
                if ($imageFile) {
                    $model->setData('newcollections_filename_2', $imageFile);
                }
            }

            if ($is_delete_newcollections_0 && $model->getData('newcollections_filename_3')) {
                $model->setData('newcollections_filename_3', '');
            } else {
                $imageFile = $imageHelper->uploadImage('newcollections_filename_3', 'newcollections_3');
                if ($imageFile) {
                    $model->setData('newcollections_filename_3', $imageFile);
                }
            }

            // if ($is_delete_newcollections_tablet && $model->getNewcollectionsFilenameTablet()) {
            //     $model->setNewcollectionsFilenameTablet('');
            // } else {
            //     $imageFiletablet = $imageHelper->uploadImage('newcollections_filename_tablet', 'newcollections');
            //     if ($imageFiletablet) {
            //         $model->setNewcollectionsFilenameTablet($imageFiletablet);
            //     }
            // }

            $model->setData('storeview_id', null);
            $model->save();
            $this->updateVisibility($simiObjectManager, $model, $data);

            $this->messageManager->addSuccess(__('The Data has been saved.'));
            $simiObjectManager->get('Magento\Backend\Model\Session')->setFormData(false);

            $simiObjectManager->get('Simi\Simicustomize\Helper\Data')->flushStaticCache();
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', ['newcollections_id' => $model->getId(), '_current' => true]);
                return;
            }
            $this->_redirect('*/*/');
            return;
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
        }

        $this->_getSession()->setFormData($data);
        $this->_redirect('*/*/edit', ['newcollections_id' => $this->getRequest()->getParam('newcollections_id')]);
    }
    
    private function updateVisibility($simiObjectManager, $model, $data)
    {
        $simicustomizehelper = $simiObjectManager->get('Simi\Simiconnector\Helper\Data');
        if ($data['storeview_id'] && is_array($data['storeview_id'])) {
            $typeID            = $simicustomizehelper->getVisibilityTypeId('homecategory');
            $visibleStoreViews = $simiObjectManager
                    ->create('Simi\Simiconnector\Model\Visibility')->getCollection()
                    ->addFieldToFilter('content_type', $typeID)
                    ->addFieldToFilter('item_id', $model->getId());
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

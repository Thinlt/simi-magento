<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Store;

use Simi\Simistorelocator\Controller\Adminhtml\Store;

class ImportProcess extends Store {

    public function execute() {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (isset($_FILES['filecsv'])) {
            if (substr($_FILES['filecsv']["name"], -4) != '.csv') {
                $this->messageManager->addError(__('Please choose a CSV file'));
                return $resultRedirect->setPath('*/*/importstore');
            }

            $fileName = $_FILES['filecsv']['tmp_name'];
            $csvObject = $this->_objectManager->create('Magento\Framework\File\Csv');
            $helperRegion = $this->_objectManager->create('Simi\Simistorelocator\Helper\Region');
            $data = $csvObject->getData($fileName);

            $store = $this->_createMainModel();

            $storeData = array();

            try {
                $total = 0;
                $error_message = '';
                $flag = 1;
                foreach ($data as $col => $row) {
                    if ($col == 0) {
                        $index_row = $row;
                    } else {

                        for ($i = 0; $i < count($row); $i++) {
                            $storeData[$index_row[$i]] = $row[$i];
                        }

                        if (isset($storeData['country_id']) && isset($storeData['state']))
                            $storeData['state_id'] = $helperRegion->validateState(
                                    $storeData['country_id'],
                                    $storeData['state']
                                );

                        if (isset($storeData['state_id']) 
                                && $storeData['state_id'] == \Simi\Simistorelocator\Helper\Region::STATE_ERROR) {
                            $_state = $storeData['state_id'] == ''
                                    || $storeData['state_id'] == -1? 'null'
                                    : $storeData['state_id'];
                            $error_message .= ' <br />' . $flag . ': ' . $_state
                                    . ' of <strong>' . $storeData['store_name']
                                    . '</strong>';
                            $flag++;
                        }

                        if (isset($storeData['state_id']))
                            $_state_id = $storeData['state_id'] > \Simi\Simistorelocator\Helper\Region::STATE_ERROR;

                        if (isset($storeData['store_name']) && $storeData['store_name'] &&
                                isset($storeData['address']) && $storeData['address'] &&
                                isset($storeData['country_id']) && $storeData['country_id'] && isset($_state_id) && $_state_id) {
                            $storeData['meta_title'] = $storeData['store_name'];
                            $storeData['meta_keywords'] = $storeData['store_name'];
                            $storeData['meta_description'] = $storeData['store_name'];
                            $store->setData($storeData);
                            $store->setId(null);

                            if ($store->import()) {
                                $total++;
                            }
                        }
                    }
                }

                if ($error_message != '') {
                    $error_msg = 'The States that don\'t match any State: ' . $error_message;
                    $this->messageManager->addNotice($error_msg);
                }

                if ($total != 0) {
                    $this->messageManager->addSuccess('Imported successful total ' . $total . ' stores');
                } else {
                    $this->messageManager->addSuccess('No store imported');
                }

                return $resultRedirect->setPath('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/importstore');
            }
        }
    }

    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Simi_Simistorelocator::storelocator');
    }

}

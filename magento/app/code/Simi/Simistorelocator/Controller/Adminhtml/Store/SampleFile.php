<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Store;

use Magento\Framework\App\Filesystem\DirectoryList;

class SampleFile extends \Simi\Simistorelocator\Controller\Adminhtml\Store {

    /**
     * Execute action
     */
    public function execute() {
        $fileName = 'simistorelocator.csv';

        /** @var \Magento\Framework\App\Response\Http\FileFactory $fileFactory */
        $fileFactory = $this->_objectManager->get('Magento\Framework\App\Response\Http\FileFactory');

        return $fileFactory->create(
                        $fileName, $this->getStorelocatorSampleData(), DirectoryList::VAR_DIR
        );
    }

    public function getStorelocatorSampleData() {
        /** @var \Magento\Framework\Module\Dir $moduleReader */
        $moduleReader = $this->_objectManager->get('Magento\Framework\Module\Dir');
        /** @var \Magento\Framework\Filesystem\DriverPool $drivePool */
        $drivePool = $this->_objectManager->get('Magento\Framework\Filesystem\DriverPool');
        $drive = $drivePool->getDriver(\Magento\Framework\Filesystem\DriverPool::FILE);

        return $drive->fileGetContents($moduleReader->getDir('Simi_Simistorelocator')
                        . DIRECTORY_SEPARATOR . '_fixtures' . DIRECTORY_SEPARATOR . 'simistorelocator.csv');
    }

}

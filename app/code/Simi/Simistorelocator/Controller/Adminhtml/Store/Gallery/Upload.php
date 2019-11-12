<?php

namespace Simi\Simistorelocator\Controller\Adminhtml\Store\Gallery;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;

class Upload extends \Simi\Simistorelocator\Controller\Adminhtml\Store {

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute() {
        try {
            /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
            $uploader = $this->_objectManager->get('Simi\Simistorelocator\Model\ImageUploaderFactory')
                    ->create(['fileId' => 'image']);

            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);

            $result = $uploader->save(
                    $mediaDirectory->getAbsolutePath(\Simi\Simistorelocator\Model\Image::IMAGE_GALLERY_PATH)
            );

            $this->_eventManager->dispatch(
                    'simistorelocator_store_gallery_upload_image_after', ['result' => $result, 'action' => $this]
            );

            unset($result['tmp_name']);
            unset($result['path']);

            $result['url'] = $this->imageHelper->getMediaUrlImage(
                    \Simi\Simistorelocator\Model\Image::IMAGE_GALLERY_PATH . $uploader->getUploadedFileName()
            );
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));

        return $response;
    }
}

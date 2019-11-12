<?php

namespace Simi\Simistorelocator\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;


class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * default small image size.
     */
    const SMALL_IMAGE_SIZE_WIDTH = 40;
    const SMALL_IMAGE_SIZE_HEIGHT = 30;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    public $mediaDirectory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Simi\Simistorelocator\Model\ImageUploaderFactory
     */
    public $imageUploaderFactory;

    /**
     * Block constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Simi\Simistorelocator\Model\ImageUploaderFactory $imageUploaderFactory
    ) {
        parent::__construct($context);

        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        $this->imageUploaderFactory = $imageUploaderFactory;
    }

    /**
     * get media url of image.
     *
     * @param string $imagePath
     *
     * @return string
     */
    public function getMediaUrlImage($imagePath = '')
    {
        return $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $imagePath;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @param $fileId
     * @param $relativePath
     *
     * @throws LocalizedException
     */
    public function mediaUploadImage(
        \Magento\Framework\Model\AbstractModel $model,
        $fileId,
        $relativePath,
        $makeResize = false
    ) {
        $imageRequest = $this->_getRequest()->getFiles($fileId);
        if ($imageRequest) {
            if (isset($imageRequest['name'])) {
                $fileName = $imageRequest['name'];
            } else {
                $fileName = '';
            }
        } else {
            $fileName = '';
        }

        if ($imageRequest && strlen($fileName)) {
            try {
                /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
                $uploader = $this->imageUploaderFactory->create(['fileId' => $fileId]);
                $mediaAbsolutePath = $this->mediaDirectory->getAbsolutePath($relativePath);
                $uploader->save($mediaAbsolutePath);

                /*
                 * resize to small image
                 */
                if ($makeResize) {
                    $this->resizeImage(
                        $mediaAbsolutePath . $uploader->getUploadedFileName(),
                        self::SMALL_IMAGE_SIZE_WIDTH
                    );
                    $imagePath = $this->getResizeImageFileName($relativePath . $uploader->getUploadedFileName());
                } else {
                    $imagePath = $relativePath . $uploader->getUploadedFileName();
                }

                $model->setData($fileId, $imagePath);
            } catch (\Exception $e) {
                throw new LocalizedException(
                    __($e->getMessage())
                );
            }
        } else {
            if ($model->getData($fileId) && empty($model->getData($fileId . '/delete'))) {
                $model->setData($fileId, $model->getData($fileId . '/value'));
            } else {
                $model->setData($fileId, '');
            }
        }
    }

    /**
     * resize image.
     *
     * @param $fileName
     * @param $width
     * @param null $height
     */
    protected function _resizeImage($fileName, $width, $height = null)
    {
        /** @var \Magento\Framework\Image $image */
        $image = $this->objectManager->create('Magento\Framework\Image', ['fileName' => $fileName]);

        $image->constrainOnly(true);
        $image->keepAspectRatio(true);
        $image->keepFrame(false);
        $image->resize($width, $height);
        $image->save($this->_getResizeImageFileName($fileName));
    }

    /**
     * @param $fileName
     *
     * @return string
     */
    protected function _getResizeImageFileName($fileName)
    {
        return dirname($fileName) . '/resize/' . basename($fileName);
    }
}

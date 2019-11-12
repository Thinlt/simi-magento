<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Widget\Form\Element;

class Gallery extends \Magento\Framework\Data\Form\Element\AbstractElement {

    /**
     * Registry object.
     *
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
     * @var \Magento\Framework\File\Size
     */
    public $fileConfig;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $jsonHelper;

    /**
     * @var \Simi\Simistorelocator\Model\ResourceModel\Image\CollectionFactory
     */
    public $imageCollectionFactory;

    /**
     * @var \Simi\Simistorelocator\Helper\Image
     */
    public $imageHelper;

    /**
     * @var \Simi\Simistorelocator\Model\SystemConfig
     */
    public $systemConfig;

    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\UrlFactory $backendUrlFactory,
        \Magento\Framework\File\Size $fileConfig,
        \Simi\Simistorelocator\Helper\Image $imageHelper,
        \Simi\Simistorelocator\Model\ResourceModel\Image\CollectionFactory $imageCollectionFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Simi\Simistorelocator\Model\SystemConfig $systemConfig,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);

        $this->backendUrl = $backendUrlFactory->create();
        $this->fileConfig = $fileConfig;
        $this->coreRegistry = $coreRegistry;
        $this->jsonHelper = $jsonHelper;
        $this->imageCollectionFactory = $imageCollectionFactory;
        $this->imageHelper = $imageHelper;
        $this->systemConfig = $systemConfig;
    }

    /**
     * Get label.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel() {
        return __('Images');
    }

    /**
     * get images json data of store.
     *
     * @return string
     */
    public function getImageJsonData() {
        /** @var \Simi\Simistorelocator\Model\Store $store */
        $store = $this->coreRegistry->registry('simistorelocator_store');

        $imageArray = [];
        foreach ($store->getImages() as $image) {
            $imageData = [
                'file' => $image->getPath(),
                'url' => $this->imageHelper->getMediaUrlImage($image->getPath()),
                'image_id' => $image->getId(),
            ];

            if ($store->getBaseimageId() == $image->getId()) {
                $imageData['base'] = 1;
            }

            $imageArray[] = $imageData;
        }

        return $this->jsonHelper->jsonEncode($imageArray);
    }

    /**
     * Get url to upload files.
     *
     * @return string
     */
    public function getUploadUrl() {
        return $this->backendUrl->getUrl('simistorelocatoradmin/store_gallery/upload');
    }

    /**
     * Get maximum file size to upload in bytes.
     *
     * @return int
     */
    public function getFileMaxSize() {
        return $this->fileConfig->getMaxFileSize();
    }

    /**
     * get maximum image count.
     *
     * @return mixed
     */
    public function getMaximumImageCount() {
        return $this->systemConfig->getMaxImageGallery();
    }
}

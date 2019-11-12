<?php

namespace Simi\Simistorelocator\Model;

class ImageUploaderFactory {

    /**
     * Object Manager instance.
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager = null;

    /**
     * Instance name to create.
     *
     * @var string
     */
    public $instanceName = null;

    /**
     * Factory constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string                                    $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = 'Magento\MediaStorage\Model\File\Uploader'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters.
     *
     * @param array $data
     *
     * @return \Simi\Simistorelocator\Model\Image
     */
    public function create(array $data = []) {
        $uploader = $this->objectManager->create($this->instanceName, $data);

        if (!$uploader instanceof \Magento\MediaStorage\Model\File\Uploader) {
            throw new \Magento\Framework\Exception\LocalizedException(
            __('The class uploader is invalid !')
            );
        }

        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

        /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
        $imageAdapter = $this->objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
        $uploader->addValidateCallback('simistorelocator', $imageAdapter, 'validateUploadFile');
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);

        return $uploader;
    }

}

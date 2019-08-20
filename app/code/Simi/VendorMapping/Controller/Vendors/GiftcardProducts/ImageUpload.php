<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Controller\Vendors\GiftcardProducts;

use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Vnecoms\Vendors\App\Action\Context;

/**
 * Class ImageUpload
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Product
 */
class ImageUpload extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Simi_VendorMapping::giftcard_products';

    /**
     * Field ID for file saving
     */
    const FILE_ID_FIELD = 'image';

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @param Context $context
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Context $context,
        ImageUploader $imageUploader //Simi\VendorMapping\ImageUploader object in aw giftcard etc/di.xml
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    /**
     * Image uploader action
     *
     * @return Json
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $result = $this->imageUploader->saveFileToTmpDir(self::FILE_ID_FIELD);
        } catch (\Exception $exception) {
            $result = [
                'error' => $exception->getMessage(),
                'errorcode' => $exception->getCode()
            ];
        }
        return $resultJson->setData($result);
    }
}

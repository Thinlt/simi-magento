<?php

namespace Vnecoms\Vendors\Model;

use Magento\Framework\Image as MagentoImage;

class Image extends \Magento\Catalog\Model\Product\Image
{
    const VENDOR_BASE_MEDIA_PATH = 'ves_vendors/media';

    /**
     * Get base media path
     *
     * @return string;
     */
    public function getBaseMediaPath()
    {
        $path = $this->getData('base_media_path');
        if (!$path) {
            return self::VENDOR_BASE_MEDIA_PATH;
        }
        
        return $path;
    }

}

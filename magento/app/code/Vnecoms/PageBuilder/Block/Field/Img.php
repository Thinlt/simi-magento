<?php
namespace Vnecoms\PageBuilder\Block\Field;

use Magento\Framework\UrlInterface;

class Img extends \Vnecoms\PageBuilder\Block\Field\AbstractField
{
    /**
     * Get Image Url
     * 
     * @return string
     */
    public function getImageUrl(){
        $imgFile = $this->getData('imgFile');
        switch($this->getData('imgType')){
            case 'media':
                return $this->_storeManager->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA).$imgFile;
            case 'static':
                return $this->getViewFileUrl($imgFile);
            case 'url':
                return $imgFile;
            default: 
                return $this->getViewFileUrl($imgFile);
        }
    }
}

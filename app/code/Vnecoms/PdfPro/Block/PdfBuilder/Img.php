<?php
namespace Vnecoms\PdfPro\Block\PdfBuilder;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Filesystem\DirectoryList;

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
                $mediaDirectory = ObjectManager::getInstance()->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);
                return $mediaDirectory->getAbsolutePath($imgFile);
            case 'static':
                return $this->getImageFilePath($imgFile);
            default: 
                return $this->getImageFilePath($imgFile);
        }
    }
    
    /**
     * Get image file path
     * 
     * @param string $imgFile
     * @return string
     */
    public function getImageFilePath($imgFile){
        $moduleReader = ObjectManager::getInstance()->create('Magento\Framework\Module\Dir\Reader');
        $fileInfo = explode("/", $imgFile);
        $moduleName = $fileInfo[0];
        unset($fileInfo[0]);
        $fileInfo = implode("/", $fileInfo);
        $viewDir = $moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
            $moduleName
        );
        return $viewDir . '/base/web/'.$fileInfo;
    }
}

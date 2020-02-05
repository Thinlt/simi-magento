<?php
namespace Vnecoms\PageBuilder\Controller\Adminhtml\Image;

use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Backend::admin';

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            $url = $this->getRequest()->getParam('image');
            if(!$url) throw new \Exception(__("The image URL is not valid"));
            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
            
            $config = $this->_objectManager->get('Vnecoms\PageBuilder\Model\Media\Config');
            $path = 'vnecoms_pagebuilder/media';
            $absolutePath = $mediaDirectory->getAbsolutePath($path);
            $fileName = explode('/', $url);
            $fileName = end($fileName);
            
            $ch = curl_init($url);
            $fp = fopen($absolutePath.'/'.$fileName, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);

            $result = [
                'name' => $fileName,
                'error' => false,
                'file' => $fileName,
                'url' => $this->_objectManager->get('Vnecoms\PageBuilder\Model\Media\Config')->getMediaUrl($fileName),
                'img_type' => 'media',
                'img_file' => $path.'/'.$fileName
            ];

        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 13/01/2017
 * Time: 09:19
 */

namespace Vnecoms\PdfPro\Controller\Adminhtml\Key;
use \Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Duplicate extends \Magento\Backend\App\Action
{

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $_filesystem;

    /** @var \Magento\Framework\UrlInterface */
    protected $url;

    protected $_mediaConfig;

    /** @var \Vnecoms\PdfPro\Helper\Data  */
    protected $pdfHelper;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /** @var \Magento\Store\Model\StoreManagerInterface  */
    protected $_storeManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Vnecoms\PdfPro\Helper\Data $pdfHelper,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        parent::__construct($context);
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_storeManager = $storeManager;
        $this->pdfHelper = $pdfHelper;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        /** @var \Vnecoms\PdfPro\Model\Key $model */
        $model = $this->_objectManager->create('Vnecoms\PdfPro\Model\Key');

        if ($id) {
            try {
                $key = $model->load($id);
                $newKey = clone $model;
                $data = $key->getData();
                //var_dump($data);die;
                unset($data['entity_id']);
                $data['api_key'] = $data['api_key']."-".uniqid();

                //$extension_pos = strrpos($data['logo'], '.');
                //$pathCopy = substr($data['logo'], 0, $extension_pos) . '-copy' . substr($data['logo'], $extension_pos);
                // Dupilcate image

                /*$originalImageFile = $this->pdfHelper->getBaseUrl('ves_pdfpro/logos/'.$data['logo']);
                $duplicateImageFile = $this->pdfHelper->getBaseUrl('ves_pdfpro/logos/'.$pathCopy);
                $mediaPath = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $logoDirPath = $mediaPath.'ves_pdfpro/logos/';
                $targetPath = $this->mediaDirectory->getAbsolutePath('media/ves_pdfpro/logos');*/

                //$this->mediaDirectory->copyFile('ves_pdfpro/logos/'.$data['logo'], 'ves_pdfpro/logos/'.$pathCopy, $this->mediaDirectory);

                //$data['logo'] = $pathCopy;

                $newKey->isObjectNew(true);
                $newKey->setData($data);
                $newKey->save();

                $this->messageManager->addSuccessMessage(__('You duplicated the key.'));
                $resultRedirect->setPath('*/*/edit', ['_current' => true, 'id' => $newKey->getId()]);
            } catch (\Exception $e) {
                // if something is going wrong: catch exception & redirect back
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('*/*/edit', ['_current' => true]);
            }
        }

        return $resultRedirect;
    }
}

<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Vnecoms\PdfPro\Controller\Adminhtml\Template;
use Vnecoms\PdfPro\Model\TemplateFactory;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\Model\View\Result\RedirectFactory;

/**
 * Class Upload.
 *
 * @author VnEcoms team <vnecoms.com>
 */
class Upload extends Template
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $resultPage;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * Upload constructor.
     *
     * @param \Vnecoms\PdfPro\Helper\Data        $helper
     * @param PageFactory                                      $resultPageFactory
     * @param \Magento\Framework\Controller\Result\RawFactory  $resultRawFactory
     * @param Registry                                         $registry
     * @param TemplateFactory                                  $templateFactory
     * @param RedirectFactory                                  $resultRedirectFactory
     * @param \Magento\Framework\View\Result\LayoutFactory     $resultLayoutFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param Context                                          $context
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        Registry $registry,
        TemplateFactory $templateFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        Context $context
    ) {
        $this->helper = $helper;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($registry, $templateFactory, $resultLayoutFactory, $fileFactory, $context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $result = array();
        if ($_FILES['file']['name'] and $_FILES['file']['name'] != '') {
            try {
                $uploader = $this->_objectManager->create(
                    'Magento\MediaStorage\Model\File\Uploader',
                    ['fileId' => 'file']
                );

                $uploader->setAllowedExtensions(['zip']);
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(true);
                /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);
                $config = $this->_objectManager->get('Magento\Catalog\Model\Product\Media\Config');

                if (!is_dir($mediaDirectory->getAbsolutePath('ves_pdfpro/tmp/templates'))) {
                    mkdir($mediaDirectory->getAbsolutePath('ves_pdfpro/tmp/templates'), 0777, true);
                }

                $result = $uploader->save($mediaDirectory->getAbsolutePath('ves_pdfpro/tmp/templates'));

                unset($result['tmp_name']);
                unset($result['path']);

                $result['url'] = $this->getTmpMediaUrl($result['file']);
                $result['file_path'] = $this->getTmpMediaPath($result['file']);
                $result['path'] = $this->helper->getBaseDirMedia('ves_pdfpro/templates');
                $result['file'] = $result['file'].'.tmp';
            } catch (\Exception $e) {
                $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
            }
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));

        return $response;
    }

    /**
     * instantiate result page object.
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function getResultPage()
    {
        if (is_null($this->resultPage)) {
            $this->resultPage = $this->resultPageFactory->create();
        }

        return $this->resultPage;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function getTmpMediaUrl($file)
    {
        return $this->getBaseTmpMediaUrl().'/'.$this->_prepareFile($file);
    }

    /**
     * @param $file
     *
     * @return string
     */
    public function getTmpMediaPath($file)
    {
        return $this->helper->getBaseDirMedia('ves_pdfpro/tmp/templates').'/'.$this->_prepareFile($file);
    }

    /**
     * @return string
     */
    public function getBaseTmpMediaUrl()
    {
        return $this->helper->getBaseUrlMedia('ves_pdfpro/tmp/templates');
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::theme');
    }
}

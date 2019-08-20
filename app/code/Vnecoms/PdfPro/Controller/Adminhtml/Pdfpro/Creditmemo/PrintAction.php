<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Pdfpro\Creditmemo;

use Vnecoms\PdfPro\Helper\Data as Helper;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;

/**
 * Class PrintAction.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class PrintAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Magento\Framework\App\Response\Http\FileFactory  $fileFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        Helper $helper,
        CreditmemoRepositoryInterface $creditmemoRepository
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
        $this->creditmemoRepository = $creditmemoRepository;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::pdfpro_print');
    }

    /**
     * @return ResponseInterface|void
     */
    public function execute()
    {
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
        if ($creditmemoId) {
            $creditmemo = $this->_objectManager->create('Magento\Sales\Api\CreditmemoRepositoryInterface')->get($creditmemoId);

            if ($creditmemo) {
                $creditmemoData = $this->_objectManager->create('\Vnecoms\PdfPro\Model\Order\Creditmemo')->initCreditmemoData($creditmemo);
             //   die();
                $resultRedirect = $this->resultRedirectFactory->create();
                try {
                    $result = $this->helper->initPdf(array($creditmemoData), 'creditmemo');
                    if ($result['success']) {
                        return $this->_fileFactory->create(
                            $this->helper->getFileName('creditmemo', $creditmemo).'.pdf',
                            $result['content'],
                            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                            'application/pdf'
                        );
                    } else {
                        throw new \Exception($result['msg']);
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $resultRedirect->setPath('sales/creditmemo/view/creditmemo_id/'.$creditmemoId);

                    return $resultRedirect;
                }
            }
        } else {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}

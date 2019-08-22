<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsShipping\Controller\Adminhtml\Sales\Creditmemo;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Api\CreditmemoRepositoryInterface;

class PrintAction extends \Magento\Sales\Controller\Adminhtml\Creditmemo\AbstractCreditmemo\PrintAction
{
    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        CreditmemoRepositoryInterface $creditmemoRepository,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
    ) {
        $this->creditmemoLoader = $creditmemoLoader;
        parent::__construct(
            $context,
            $fileFactory,
            $resultForwardFactory,
            $creditmemoRepository
        );
    }

    /**
     * Create pdf for current creditmemo
     *
     * @return ResponseInterface|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $this->creditmemoLoader->setOrderId($this->getRequest()->getParam('order_id'));
        $this->creditmemoLoader->setCreditmemoId($this->getRequest()->getParam('creditmemo_id'));
        $this->creditmemoLoader->setCreditmemo($this->getRequest()->getParam('creditmemo'));
        $this->creditmemoLoader->setInvoiceId($this->getRequest()->getParam('invoice_id'));
        $this->creditmemoLoader->load();
        /** @see \Magento\Sales\Controller\Adminhtml\Order\Invoice */
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
        if ($creditmemoId) {
            $creditmemo = $this->creditmemoRepository->get($creditmemoId);
            if ($creditmemo) {
                $pdf = $this->_objectManager->create(
                    'Vnecoms\VendorsSales\Model\Order\Pdf\Creditmemo'
                )->getPdf(
                    [$creditmemo]
                );
                $date = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->date('Y-m-d_H-i-s');
                return $this->_fileFactory->create(
                    'creditmemo' . $date . '.pdf',
                    $pdf->render(),
                    DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            }
        } else {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
    }
}

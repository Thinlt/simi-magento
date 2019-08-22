<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsShipping\Controller\Adminhtml\Sales\Creditmemo;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Vnecoms\VendorsSales\Model\Order\Pdf\Creditmemo;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory;

/**
 * Class Pdfcreditmemos
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Pdfcreditmemos extends \Magento\Sales\Controller\Adminhtml\Creditmemo\AbstractCreditmemo\Pdfcreditmemos
{
    /**
     * @param AbstractCollection $collection
     * @return ResponseInterface
     * @throws \Exception
     * @throws \Zend_Pdf_Exception
     */
    public function massAction(AbstractCollection $collection)
    {

        $pdf = $this->_objectManager->create(
            'Vnecoms\VendorsSales\Model\Order\Pdf\Creditmemo'
        );
        return $this->fileFactory->create(
            sprintf('creditmemo%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
            $pdf->getPdf($collection)->render(),
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}

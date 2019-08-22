<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsShipping\Controller\Adminhtml\Sales\Shipment;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Pdfshipments extends \Magento\Sales\Controller\Adminhtml\Shipment\AbstractShipment\Pdfshipments
{

    /**
     * @param AbstractCollection $collection
     * @return $this|ResponseInterface
     * @throws \Exception
     */
    public function massAction(AbstractCollection $collection)
    {
        
        $pdf = $this->_objectManager->create(
            'Vnecoms\VendorsSales\Model\Order\Pdf\Shipment'
        );

        return $this->fileFactory->create(
            sprintf('packingslip%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
            $pdf->getPdf($collection)->render(),
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}

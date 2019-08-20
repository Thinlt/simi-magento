<?php

namespace Vnecoms\PdfPro\Ui\Component\Listing\DataProviders\Pdfpro\Template;

class Listing extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Vnecoms\PdfPro\Model\ResourceModel\Template\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}

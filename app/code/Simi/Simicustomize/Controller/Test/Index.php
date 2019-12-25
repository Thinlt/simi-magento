<?php

namespace Simi\Simicustomize\Controller\Test;

use Magento\Framework\App\Action\Context;


class Index extends \Magento\Framework\App\Action\Action
{
    protected $vendorFactory;

    public function __construct(
        Context $context,
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory
    ) {
        parent::__construct($context);
        $this->vendorFactory = $vendorFactory;
    }

    public function execute()
    {
        $resultPage = $this->vendorFactory->create();
        $collection = $resultPage->getCollection(); //Get Collection of module data
        var_dump($collection->getData());
    }
}

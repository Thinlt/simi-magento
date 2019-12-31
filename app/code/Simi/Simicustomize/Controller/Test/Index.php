<?php

namespace Simi\Simicustomize\Controller\Test;

use Magento\Framework\App\Action\Context;


class Index extends \Magento\Framework\App\Action\Action
{
    protected $vendorFactory;

    public function __construct(
        Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        parent::__construct($context);
        $this->customerFactory = $customerFactory;
    }

    public function execute()
    {
        $resultPage = $this->customerFactory->create();
        $collection = $resultPage->getCollection(); //Get Collection of module data
        var_dump($collection->getData());
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Controller\Preorder;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Framework\App\ObjectManager;
use Simi\Simiconnector\Model\Resolver\Products\DataProvider\Product;
use Magento\Framework\Api\SearchCriteriaInterface;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Review\Model\RatingFactory;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;

class Test extends \Magento\Framework\App\Action\Action
{
    /**
     * Payment map payment_code with url path
     */
    public $paymentMap = [
        'paypal_express' => 'paypal/express/start',
        'paypal_express_bml' => 'paypal/bml/start',
    ];
    
    /**
     * @var Magento\Quote\Api\Data\PaymentInterface
     */
    protected $paymentMethod;
    /**
     * @var Magento\Quote\Api\Data\AddressInterface
     */
    protected $billingAddress;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    protected $product;
    protected $searchCriteria;

    protected $_storeManager;
    protected $_ratingFactory;
    protected $_reviewFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        \Magento\Sales\Model\Order $order,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress,
        Product $product,
        SearchCriteriaInterface $searchCriteria,
        StoreManagerInterface $storeManager,
        RatingFactory $ratingFactory,
        CollectionFactory $reviewFactory
    ){
        parent::__construct($context);
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->paymentMethod = $paymentMethod;
        $this->billingAddress = $billingAddress;
        $this->_checkoutSession = $checkoutSession;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        $this->order = $order;
        $this->product = $product;
        $this->searchCriteria = $searchCriteria;

        $this->_storeManager = $storeManager;
        $this->_ratingFactory = $ratingFactory;
        $this->_reviewFactory = $reviewFactory;
    }

    /**
     * Params $orderId increment_id
     * @return void
     */
    public function execute()
    {
        $searchResults = $this->product->getList([
            'currentPage' => 1,
            'pageSize' => 9,
            'simiFilter' => '{"vendor_id":"2"}'
        ], $this->searchCriteria);

        $products = $searchResults->getItems();
        // var_dump($products[8]->getId());die;
        echo '<pre>'; var_dump($products);die;
    }
}

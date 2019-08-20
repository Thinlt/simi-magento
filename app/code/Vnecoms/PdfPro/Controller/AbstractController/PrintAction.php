<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Controller\AbstractController;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class PrintAction.
 *
 * @author Vnecoms team <vnecoms.com>
 */
abstract class PrintAction extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderLoaderInterface
     */
    protected $orderLoader;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \VnEcoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Vnecoms\PdfPro\Model\Order
     */
    protected $pdfProOrder;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Cookie key for guest view
     */
    const COOKIE_NAME = 'guest-view';

    /**
     * Cookie path
     */
    const COOKIE_PATH = '/';

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;


    public function __construct(
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Vnecoms\PdfPro\Model\Order $pdfProOrder,
        OrderRepositoryInterface $orderRepository,
        \Vnecoms\PdfPro\Helper\Data $helper,
        Context $context,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria = null
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->coreRegistry = $registry;
        $this->orderRepository = $orderRepository;
        $this->pdfProOrder = $pdfProOrder;
        $this->_orderConfig = $orderConfig;
        $this->httpContext = $httpContext;
        $this->cookieManager = $cookieManager;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_fileFactory = $fileFactory;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->searchCriteriaBuilder = $searchCriteria ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
    }

    /**
     * Prepare download response.
     *
     * @param string $fileName
     * @param string $content
     * @param string $contentType
     * @param null   $contentLength
     */
    protected function _prepareDownloadResponse($fileName, $content, $contentType = 'application/octet-stream',
                                                $contentLength = null)
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    /**
     * Check can view order.
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool
     */
    protected function _canViewOrder(\Magento\Sales\Model\Order $order)
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)
            && $this->isVisible($order);
    }

    /**
     * Is order visible.
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool
     */
    protected function isVisible(\Magento\Sales\Model\Order $order)
    {
        return !in_array(
            $order->getStatus(),
            $this->_orderConfig->getInvisibleOnFrontStatuses()
        );
    }

    /**
     * Print Order Action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws
     */
    public function execute()
    {
        if (!$this->helper->getConfig('pdfpro/general/enabled') ||
            !$this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
        $orderId = (int) $this->getRequest()->getParam('order_id');
        if ($orderId) {
            /*
             * @var \Magento\Sales\Model\Order
             */
            $order = $this->_objectManager->create('Magento\Sales\Api\OrderRepositoryInterface')->get($orderId);

            if (!$order->getId()) {
                return $this->resultForwardFactory->create()->forward('noroute');
            }

            $orderData = $this->pdfProOrder->initOrderData($order);
            $resultRedirect = $this->resultRedirectFactory->create();
            try {
                $result = $this->helper->initPdf(array($orderData), 'order');
                if ($result['success']) {
                    return $this->_fileFactory->create(
                        $this->helper->getFileName('order', $order).'.pdf',
                        $result['content'],
                        \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                        'application/pdf'
                    );
                } else {
                    throw new \Exception($result['msg']);
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath('sales/order/view/order_id/'.$orderId);

                return $resultRedirect;
            }
        } else {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}


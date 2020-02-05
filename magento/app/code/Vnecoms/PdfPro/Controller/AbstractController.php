<?php

namespace VnEcoms\PdfPro\Controller;

use Magento\Framework\App\Action\Context;

/**
 * Class AbstractController.
 *
 * @author VnEcoms team <vnecoms.com>
 */
abstract class AbstractController extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * AbstractController constructor.
     *
     * @param Context                             $context
     * @param \Magento\Sales\Model\Order\Config   $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        parent::__construct($context);
        $this->_orderConfig = $orderConfig;
        $this->httpContext = $httpContext;
    }

    /**
     * execute function.
     */
    public function execute()
    {
        echo __('Not support');
        die();
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
}

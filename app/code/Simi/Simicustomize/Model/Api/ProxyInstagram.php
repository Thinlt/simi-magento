<?php
namespace Simi\Simicustomize\Model\Api;

class ProxyInstagram implements \Simi\Simicustomize\Api\ContactInterface
{
    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    )
    {
        $this->request = $request;
        $this->simiObjectManager = $simiObjectManager;
        return $this;
    }

    /**
     * Save Reserve request
     * @return boolean
     */
    public function index() {
        $proxy = $this->simiObjectManager->get('\Simi\Simicustomize\Model\Proxy');
        $path = $this->request->getParam('path');
        $url = 'https://www.instagram.com/'.$path;
        die($proxy->query($url));
        // return ['data' => $proxy->query($url)];
    }
}

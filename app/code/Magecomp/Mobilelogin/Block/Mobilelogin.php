<?php
namespace Magecomp\Mobilelogin\Block;

use Magecomp\Mobilelogin\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class Mobilelogin extends \Magento\Framework\View\Element\Template
{
    protected $_helper;
    protected $_StoreManagerInterface;

    public function __construct(Context $context, Data $helper, StoreManagerInterface $storemanagerinterface)
    {
        $this->_helper = $helper;
        $this->_StoreManagerInterface = $storemanagerinterface;
        parent::__construct($context);
    }

    public function getLayoutType()
    {
        return $this->_helper->getLayoutType();
    }

    public function getLoginType()
    {
        return $this->_helper->getLoginType();
    }

    public function getOtpStringlenght()
    {
        return $this->_helper->getOtpStringlenght();
    }

    public function getTemplateImage()
    {
        return $this->_helper->getTemplateImage();
    }

    public function getImageType()
    {
        return $this->_helper->getImageType();
    }

    public function getMediaUrl()
    {
        $currentStore = $this->_StoreManagerInterface->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }

    public function isEnable()
    {
        return $this->_helper->isEnable();
    }


}
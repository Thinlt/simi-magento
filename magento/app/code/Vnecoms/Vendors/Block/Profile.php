<?php
namespace Vnecoms\Vendors\Block;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class View
 * @package Vnecoms\Vendors\Block\Profile
 */
class Profile extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_vendorHelper;
    
    /**
     * @var \Vnecoms\VendorsConfig\Helper\Data
     */
    protected $_configHelper;
    
    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $_fileStorageDatabase;
    
    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $mediaDirectory;
    
    /**
     * @var \Vnecoms\Vendors\Helper\Image
     */
    protected $_imageHelper;
    
    /**
     * @var \Vnecoms\VendorsSales\Model\ResourceModel\OrderFactory
     */
    protected $_orderResourceFactory;
    
    /**
     * @var \Magento\Cms\Model\Template\Filter
     */
    protected $_filter;
    
    
    /**
     * @var \Vnecoms\Vendors\Model\VendorFactory
     */
    protected $_vendorFactory;
    
    /**
     * Constructor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Vnecoms\Vendors\Helper\Data $vendorHelper
     * @param \Vnecoms\VendorsConfig\Helper\Data $configHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDatabase
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Vnecoms\Vendors\Helper\Image $imageHelper
     * @param \Vnecoms\VendorsSales\Model\ResourceModel\OrderFactory $orderResourceFactory
     * @param \Magento\Cms\Model\Template\Filter $filter
     * @param \Vnecoms\Vendors\Model\VendorFactory $vendorFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vnecoms\Vendors\Helper\Data $vendorHelper,
        \Vnecoms\VendorsConfig\Helper\Data $configHelper,
        \Magento\Framework\Registry $registry,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDatabase,
        \Vnecoms\Vendors\Helper\Image $imageHelper,
        \Vnecoms\VendorsSales\Model\ResourceModel\OrderFactory $orderResourceFactory,
        \Magento\Cms\Model\Template\Filter $filter,
        \Vnecoms\Vendors\Model\VendorFactory $vendorFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_vendorHelper = $vendorHelper;
        $this->_configHelper = $configHelper;
        $this->_fileStorageDatabase = $fileStorageDatabase;
        $this->_mediaDirectory = $context->getFilesystem()->getDirectoryRead(DirectoryList::MEDIA);
        $this->_imageHelper = $imageHelper;
        $this->_orderResourceFactory = $orderResourceFactory;
        $this->_filter = $filter;
        $this->_vendorFactory = $vendorFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get vendor object
     *
     * @return \Vnecoms\Vendors\Model\Vendor
     */
    public function getVendor()
    {
        $vendor = $this->_coreRegistry->registry('vendor');
        if (!$vendor && $product = $this->_coreRegistry->registry('product')) {
            if ($vendorId = $product->getVendorId()) {
                $vendor = $this->_vendorFactory->create()->load($vendorId);
            }
        }
        return $vendor;
    }
    
    /**
     * Get logo width
     *
     * @return int
     */
    public function getLogoWidth()
    {
        $logoWidth = $this->getData('logo_width');
        return $logoWidth?$logoWidth:150;
    }
    
    /**
     * Get logo height
     *
     * @return int
     */
    public function getLogoHeight()
    {
        $logoHeight = $this->getData('logo_height');
        return $logoHeight?$logoHeight:150;
    }
    
    /**
     * Keep Transparency logo
     *
     * @return boolean
     */
    public function keepTransparencyLogo()
    {
        return $this->getData('keep_transparency');
    }
    
    /**
     * Get Seller Logo Image URL
     *
     * @param void
     * @return string
     */
    public function getLogoUrl()
    {
        $scopeConfig = $this->_configHelper->getVendorConfig(
            'general/store_information/logo',
            $this->getVendor()->getId()
        );
        $basePath = 'ves_vendors/logo/';
        $path =  $basePath. $scopeConfig;


        if ($scopeConfig && $this->checkIsFile($path)) {
            $this->_imageHelper->init($scopeConfig, '', [])
                ->setBaseMediaPath($basePath)
                ->keepTransparency($this->keepTransparencyLogo())
                ->backgroundColor([250,250,250])
                ->resize($this->getLogoWidth(), $this->getLogoHeight());
           
            return $this->_imageHelper->getUrl();
        }

        return $this->getNoLogoUrl();
    }
    /**
     * Get no logo image url
     * 
     * @return string
     */
    public function getNoLogoUrl(){
        return $this->getViewFileUrl('Vnecoms_Vendors::images/no-logo.jpg');
    }
    
    /**
     * Get vendor URL
     *
     * @return string
     */
    public function getVendorUrl()
    {
        return $this->getData('vendor_url');
    }
    
    /**
     * If DB file storage is on - find there, otherwise - just file_exists
     *
     * @param string $filename relative file path
     * @return bool
     */
    protected function checkIsFile($filename)
    {
        if ($this->_fileStorageDatabase->checkDbUsage() && !$this->_mediaDirectory->isFile($filename)) {
            $this->_fileStorageDatabase->saveFileToFilesystem($filename);
        }
        return $this->_mediaDirectory->isFile($filename);
    }
    
    /**
     * Get Store Name
     *
     * @return string
     */
    public function getStoreName()
    {
        return $this->_vendorHelper->getVendorStoreName($this->getVendor()->getId());
    }
    
    /**
     * Get Store Description
     *
     * @return string
     */
    public function getStoreDescription()
    {
        return $this->_vendorHelper->getVendorStoreShortDescription($this->getVendor()->getId());
    }
    
    /**
     * Can show short description
     *
     * @return boolean
     */
    public function canShowVendorShortDescription()
    {
        return $this->_vendorHelper->showVendorShortDescription();
    }
    
    /**
     * Can show short description
     *
     * @return boolean
     */
    public function canShowVendorPhone()
    {
        return $this->_vendorHelper->showVendorPhoneNumber();
    }
    
    /**
     * Get vendor phone number
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->_vendorHelper->getVendorPhone(
            $this->getVendor()->getId()
        );
    }
    
    /**
     * Can show short description
     *
     * @return boolean
     */
    public function canShowVendorOperationTime()
    {
        return $this->_vendorHelper->showVendorOperationTime();
    }
    
    /**
     * Get vendor phone number
     *
     * @return string
     */
    public function getOperationTime()
    {
        return $this->_vendorHelper->getVendorOperationTime(
            $this->getVendor()->getId()
        );
    }
    
    /**
     * Get current country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->getVendor()->getCountryName($this->_design->getLocale());
    }
    
    /**
     * Get sales count
     *
     * @return number
     */
    public function getSalesCount()
    {
        $resource = $this->_orderResourceFactory->create();
        return $resource->getSalesCount($this->getVendor()->getId());
    }
    
    /**
     * Get Joined Date
     *
     * @return string
     */
    public function getJoinedDate()
    {
        return $this->formatDate($this->getVendor()->getCreatedAt(), \IntlDateFormatter::MEDIUM);
    }
    
    /**
     * Get vendor address
     *
     * @return string
     */
    public function getAddress()
    {
        $template = $this->_vendorHelper->getAddressTemplate();
        $country =  $this->getVendor()->getCountryName(
            $this->_design->getLocale()
        );
        $variables = [
            'street' => $this->getVendor()->getStreet(),
            'city' => $this->getVendor()->getCity(),
            'country' => $country,
            'region' => $this->getVendor()->getRegion(),
            'postcode' => $this->getVendor()->getPostcode(),
        ];
        return $this->_filter->setVariables($variables)->filter($template);
    }
    
    /**
     * return nothing if the vendor object is not exist
     *
     * @see \Magento\Framework\View\Element\Template::_toHtml()
     */
    protected function _toHtml()
    {
        if (!$this->getVendor() || !$this->_vendorHelper->moduleEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }
}

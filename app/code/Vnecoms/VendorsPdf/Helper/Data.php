<?php

namespace Vnecoms\VendorsPdf\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Helper\Context;
use Vnecoms\PdfPro\Helper\Data as PdfHelper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_DEFAULT_PDF = 'vendors/sales/pdf_template';
    
    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $pdfHelper;
    
    /**
     * @param Context $context
     * @param PdfHelper $pdfHelper
     */
    public function __construct(
        Context $context,
        PdfHelper $pdfHelper
    ) {
        parent::__construct($context);
        $this->pdfHelper = $pdfHelper;
    }
    
    /**
     * Get default pdf
     * 
     * @param int $storeId
     * @return string
     */
    public function getDefaultPdf($storeId){
        $templateId = $this->scopeConfig->getValue(self::XML_PATH_DEFAULT_PDF,ScopeInterface::SCOPE_STORE, $storeId);
        $templateObj = ObjectManager::getInstance()->get('Vnecoms\PdfPro\Model\Key');
        
        return $templateObj->load($templateId)->getApiKey();
    }
    
    /**
     * Get Api Key
     * 
     * @param int $vendorId
     * @param int $storeId
     * @param int $groupId
     * @return string
     */
    public function getApiKey($vendorId, $storeId, $groupId){
        return $this->pdfHelper->getApiKey($storeId, $groupId);
        /* In this version all vendor will use same pdf invoice template*/
        return $this->getDefaultPdf($storeId);
    }
}
<?php
namespace Vnecoms\VendorsShipping\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\Order as ModelOrder;
use Magento\Sales\Api\Data\OrderInterface;

class ShippingDescription
{

    /*Characters between method and vendor_id*/
    const SEPARATOR = '||';

    /*Characters between methods*/
    const METHOD_SEPARATOR = '|_|';


    /**
     * @var \Vnecoms\Vendors\Model\Vendor
     *
     */
    protected $_vendorModel;

    /**
     * @var \Vnecoms\VendorsShipping\Helper\Data
     */
    protected $helper;
    
    /**
     * @param \Vnecoms\Vendors\Model\VendorFactory $vendor
     * @param \Vnecoms\VendorsShipping\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Vnecoms\Vendors\Model\VendorFactory $vendor,
        \Vnecoms\VendorsShipping\Helper\Data $helper,
        array $data = []
    ) {
        $this->_vendorModel = $vendor;
        $this->helper = $helper;
    }

    /**
     * Check if resource for which access is needed has self permissions defined in webapi config.
     *
     * @param \Magento\Framework\Authorization $subject
     * @param callable $proceed
     * @param string $privilege
     *
     * @return bool true If resource permission is self, to allow
     * customer access without further checks in parent method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetShippingDescription(
        ModelOrder $subject,
        \Closure $proceed
    ) {
        if(!$this->helper->isEnabled()) $proceed();
        
        $shippingDesctiption = $subject->getData(OrderInterface::SHIPPING_DESCRIPTION);

        $shippingDesctiption = preg_replace("/Multiple_Rate/is", "", $shippingDesctiption);
        $shippingDesctiption = trim($shippingDesctiption);
        $shippingDesctiption =  trim($shippingDesctiption, "-");

        $shippingDesctiption =  explode(self::METHOD_SEPARATOR, $shippingDesctiption);

        $description = "";
        foreach ($shippingDesctiption as $vendorMethod) {
            $vendorMethod = trim($vendorMethod);
            $vendorMethodList = explode(self::SEPARATOR, $vendorMethod);

            if (count($vendorMethodList) > 1) {
                $vendor = $this->_vendorModel->create()->load($vendorMethodList[1]);
                if (!$vendor->getId()) {
                    continue;
                }
                $description .= $vendor->getVendorId()." : ".$vendorMethodList[0]." | ";
            } else {
                $description .= $vendorMethodList[0]." | ";
            }
        }


        $description = trim($description);
        $description = trim($description, "|");

        return $description;
    }
}

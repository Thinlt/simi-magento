<?php
namespace Vnecoms\VendorsProduct\Model\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ViewProduct
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $_redirect;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Vendor Product helper
     * @var \Vnecoms\VendorsProduct\Helper\Data
     */
    protected $productHelper;

    /**
     * Vendor helper
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $vendorHelper;


    public function __construct(
        ProductRepositoryInterface $productRepository,
        \Vnecoms\VendorsProduct\Helper\Data $productHelper,
        \Vnecoms\Vendors\Helper\Data $helper
    ) {
        $this->productRepository = $productRepository;
        $this->productHelper = $productHelper;
        $this->vendorHelper = $helper;
    }

    /**
     * Check if resource for which access is needed has self permissions defined in webapi config.
     *
     * @param \Magento\Framework\Authorization $subject
     * @param callable $proceed
     * @param ModelProduct|int $product
     * @param string $privilege
     *
     * @return bool true If resource permission is self, to allow
     * customer access without further checks in parent method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCanShow(
        \Magento\Catalog\Helper\Product $subject,
        \Closure $proceed,
        $product
    ) {
        
        if (is_int($product)) {
            try {
                $product = $this->productRepository->getById($product);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        } else {
            if (!$product->getId()) {
                return false;
            }
        }

        $notActiveVendorIds = $this->vendorHelper->getNotActiveVendorIds();

        if (!in_array($product->getData("approval"), $this->productHelper->getAllowedApprovalStatus())
        || in_array($product->getData("vendor_id"), $notActiveVendorIds)
        ) {
            return false;
        }


        return $proceed($product);
    }
}

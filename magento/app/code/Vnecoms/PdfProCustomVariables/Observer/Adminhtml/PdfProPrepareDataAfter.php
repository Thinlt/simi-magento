<?php
namespace Vnecoms\PdfProCustomVariables\Observer\Adminhtml;

use \Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;

class PdfProPrepareDataAfter implements \Magento\Framework\Event\ObserverInterface
{

    /** @var \Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariablesFactory  */
    protected $customVariablesFactory;

    /** @var \Magento\Customer\Model\CustomerFactory  */
    protected $customerFactory;

    /** @var \Magento\Catalog\Api\ProductRepositoryInterface  */
    protected $productRepository;

    /** @var \Vnecoms\PdfProCustomVariables\Helper\Image  */
    protected $variablesHelperImage;

    /** @var \Magento\Eav\Model\Entity\AttributeFactory  */
    protected $eavAttributeFactory;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * Logger.
     *
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /** @var \Vnecoms\PdfPro\Helper\Data  */
    protected $pdfProHelper;

    /** @var \Magento\Catalog\Model\ProductFactory  */
    protected $_productFactory;

    /**
     * PdfProPrepareDataAfter constructor.
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Vnecoms\PdfProCustomVariables\Helper\Image $variablesHelperImage
     * @param \Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariablesFactory $customVariablesFactory
     * @param \Magento\Eav\Model\Entity\AttributeFactory $eavAttributeFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Vnecoms\PdfPro\Helper\Data $pdfProHelper
     * @param Logger $logger
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Vnecoms\PdfProCustomVariables\Helper\Image $variablesHelperImage,
        \Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariablesFactory $customVariablesFactory,
        \Magento\Eav\Model\Entity\AttributeFactory $eavAttributeFactory,
        \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Vnecoms\PdfPro\Helper\Data $pdfProHelper,
        Logger $logger
    ) {
        $this->customerFactory = $customerFactory;
        $this->productRepository = $productRepository;
        $this->variablesHelperImage = $variablesHelperImage;
        $this->customVariablesFactory = $customVariablesFactory;
        $this->eavAttributeFactory = $eavAttributeFactory;
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->_assetRepo = $assetRepo;
        $this->pdfProHelper = $pdfProHelper;
        $this->_productFactory = $productFactory;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {

        $type = $observer->getType();
        if ($type == 'item') {
            $itemData       = $observer->getSource();
            $item           = $observer->getModel();
            try {
                $product = $this->_productFactory->create()->load($item->getProductId());
            } catch (\Exception $e) {
                throw new NoSuchEntityException(__("Product doesn't exist."));
            }
            if (!$product) {
                return;
            }
            //$product        = $this->productRepository->getById($item->getProductId());
            $itemProduct    = new \Magento\Framework\DataObject();
            $itemCustomer = new \Magento\Framework\DataObject();

            if (!($item instanceof \Magento\Sales\Model\Order\Item)) {
                $order = $item->getOrderItem()->getOrder();
            } else {
                $order  = $item->getOrder();
            }
            $orderCurrencyCode      = $order->getOrderCurrencyCode();

            $availableAttributes    = $this->getAvailableAttributeCodesOfProduct($product);

            /** @var \Vnecoms\PdfProCustomVariables\Model\ResourceModel\PdfproCustomVariables\Collection  $collection */
            $collection = $this->customVariablesFactory->create()->getCollection();
            foreach ($collection->getData() as $data) {
                switch ($data['variable_type']) {
                    case 'attribute':
                        if ($data['attribute_id'] != 0) {
                            $attributeInfo = $this->eavAttributeFactory->create()->load($data['attribute_id']);
                        } elseif ($data['attribute_id_customer'] != 0) {
                            $attributeInfo = $this->eavAttributeFactory->create()->load($data['attribute_id_customer']);
                        }
                        switch ($attributeInfo->getFrontendInput()) {
                            case 'text':
                                isset($availableAttributes[$attributeInfo->getAttributeCode()]) ?
                                    $src = $product->getData($attributeInfo->getAttributeCode())
                                    : $src = '';
                                $itemProduct->setData($data['name'], $src);
                                break;

                            case 'textarea':
                                isset($availableAttributes[$attributeInfo->getAttributeCode()]) ?
                                    $src = $product->getData($attributeInfo->getAttributeCode())
                                    : $src = '';
                                $itemProduct->setData($data['name'], $src);
                                break;

                            case 'date':
                                if (isset($availableAttributes[$attributeInfo->getAttributeCode()])) {
                                    $date = $product->getData($attributeInfo->getAttributeCode());
                                    $dateFormated = $date ? new \Magento\Framework\DataObject([
                                        'full' => $this->dateTimeFormatter->formatObject(new \DateTime($date), \IntlDateFormatter::FULL),
                                        'long' => $this->dateTimeFormatter->formatObject(new \DateTime($date), \IntlDateFormatter::LONG),
                                        'medium' => $this->dateTimeFormatter->formatObject(new \DateTime($date), \IntlDateFormatter::MEDIUM),
                                        'short' => $this->dateTimeFormatter->formatObject(new \DateTime($date), \IntlDateFormatter::SHORT),
                                    ]):'';
                                    $itemProduct->setData($data['name'], $dateFormated);
                                } else {
                                    $itemProduct->setData($data['name'], '');
                                }
                                break;

                            case 'price':
                                isset($availableAttributes[$attributeInfo->getAttributeCode()]) ?
                                    $price = $this->pdfProHelper->currency($product->getData($attributeInfo->getAttributeCode()), $orderCurrencyCode)
                                    : $price = '';
                                $itemProduct->setData($data['name'], $price);
                                break;

                            case 'multiselect':
                                $label_arr = $product->getAttributeText($attributeInfo->getAttributeCode());
                                count($label_arr) == 0 ? $label = '' : $label = implode(',', $label_arr);
                                $itemProduct->setData($data['name'], $label);
                                break;

                            case 'select':
                                (isset($availableAttributes[$attributeInfo->getAttributeCode()]) && $product->getData($attributeInfo->getAttributeCode()) != '') ?
                                    $label = $product->getResource()->getAttribute($attributeInfo->getAttributeCode())->getFrontend()->getValue($product)
                                    : $label = '';
                                $itemProduct->setData($data['name'], $label);
                                break;

                            case 'boolean':
                                isset($availableAttributes[$attributeInfo->getAttributeCode()]) ?
                                    $label = $product->getResource()->getAttribute($attributeInfo->getAttributeCode())->getFrontend()->getValue($product)
                                    : $label = '';
                                $itemProduct->setData($data['name'], $label);
                                break;

                            case 'media_image':
                                if($product->getTypeId() == 'configurable'){
                                    /*If the product is configurable just return the image of child product.*/
                                    $childItem = null;
                                    foreach($item->getChildrenItems() as $child){
                                        $childItem = $child;
                                    }
                                    try {
                                        $product = $this->_productFactory->create()->load($childItem->getProductId());
                                    } catch (\Exception $e) {
                                        throw new NoSuchEntityException(__("Product doesn't exist."));
                                    }
                                }
                                $mediaDirectory = ObjectManager::getInstance()->get('Magento\Framework\Filesystem')
                                    ->getDirectoryRead(DirectoryList::MEDIA);

                                $productImage = null;
                                if ($product->getId()) {
                                    $productImage = $product->getImage() ? $product->getImage() : $product->getSmallImage();
                                }

                                $imageFile = $product->getData($attributeInfo->getAttributeCode());
                                $absoluteImagePath = $mediaDirectory->getAbsolutePath('catalog/product' . '/' . $imageFile);
                                if (isset($productImage)
                                    && $productImage != "no_selection"
                                    && file_exists($absoluteImagePath)) {

                                    $imageAttribute = $attributeInfo->getAttributeCode() ?
                                        $attributeInfo->getAttributeCode() : 'small_image';
                                    $imageSrc = $this->variablesHelperImage->init($product, $imageAttribute)->resize(120)->setImageFile($imageFile)->getUrl();
                                    //$model = $helper->getModel();
                                    //$imageFilePath = 'catalog/product'.$imageFile;
                                    $src = $imageSrc;

                                } elseif ((!$productImage) || ($productImage == "no_selection")) {
                                    $src = $this->getImageFilePath('Vnecoms_PdfProCustomVariables::images/product/placeholder/thumbnail.jpg');
                                }

                                $itemProduct->setData($data['name'], $src);
                                break;

                            default:
                                isset($availableAttributes[$attributeInfo->getAttributeCode()]) ?
                                    $src = $product->getData($attributeInfo->getAttributeCode())
                                    : $src = '';
                                $itemProduct->setData($data['name'], $src);
                                break;
                        }
                        break;

                    case 'static':
                        $itemData->setData($data['name'], $data['static_value']);
                        break;
                }
            }
            $itemData->setData('product', $itemProduct);
        } elseif ($type == 'customer') {
            $customerData   = $observer->getSource();
            $item           = $observer->getModel();
            $customer       = $this->customerFactory->create()->load($item->getId());
            $availableAttributes    = $this->getAvailableAttributeCodesOfCustomer($customer);
            $collection = $this->customVariablesFactory->create()->getCollection();
            // ->addFieldToFilter('variable_type', 'customer');
            foreach ($collection->getData() as $data) {
                if ($data['variable_type'] == 'customer') {
                    $attributeInfo = $this->customVariablesFactory->create()
                        ->getAttributeInfo($data['attribute_id_customer']);

                    switch ($attributeInfo['frontend_input']) {
                        case 'text':
                            isset($availableAttributes[$attributeInfo['attribute_code']]) ?
                                $src = $customer->getData($attributeInfo['attribute_code'])
                                : $src = '';
                            $customerData->setData($data['name'], $src);
                            break;

                        case 'boolean':
                            isset($availableAttributes[$attributeInfo['attribute_code']]) ?
                                ($customer->getData($attributeInfo['attribute_code']) == 1 ?  __('Yes') :  __('No')) : $label = '';

                            $customerData->setData($data['name'], $label);
                            break;

                        case 'select':
                            (isset($availableAttributes[$attributeInfo['attribute_code']]) && $customer->getData($attributeInfo['attribute_code']) != '') ?
                                $label = $customer->getResource()->getAttribute($attributeInfo['attribute_code'])->getFrontend()->getValue($customer)
                                : $label = '';
                            $customerData->setData($data['name'], $label);
                            break;

                        case 'date':
                            if (isset($availableAttributes[$attributeInfo['attribute_code']])) {
                                $date = $customer->getData($attributeInfo['attribute_code']);
                                $dateFormated = new \Magento\Framework\DataObject([
                                    'full' => $this->dateTimeFormatter->formatObject(new \DateTime($date), \IntlDateFormatter::FULL),
                                    'long' => $this->dateTimeFormatter->formatObject(new \DateTime($date), \IntlDateFormatter::LONG),
                                    'medium' => $this->dateTimeFormatter->formatObject(new \DateTime($date), \IntlDateFormatter::MEDIUM),
                                    'short' => $this->dateTimeFormatter->formatObject(new \DateTime($date), \IntlDateFormatter::SHORT),
                                ]);
                                $customerData->setData($data['name'], $dateFormated);
                            } else {
                                $customerData->setData($data['name'], '');
                            }
                            break;

                        default:
                            isset($availableAttributes[$attributeInfo['attribute_code']]) ?
                                $src = $customer->getData($attributeInfo['attribute_code'])
                                : $src = '';
                            $customerData->setData($data['name'], $src);
                            break;
                    }
                }
            }
        }
    }

    /**
     * Get all available attribute codes of product
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAvailableAttributeCodesOfProduct(\Magento\Catalog\Model\Product $product)
    {
        $attributes = $product->getAttributes();
        $result     = [];
        foreach ($attributes as $attribute) {
            $result[$attribute->getAttributeCode()] = $attribute->getAttributeCode();
        }
        return $result;
    }

    /**
     * Get all available attribute codes of customer
     * @param \Magento\Customer\Model\Customer $customer
     * @return array
     */
    public function getAvailableAttributeCodesOfCustomer(\Magento\Customer\Model\Customer $customer)
    {
        $attributes = $customer->getAttributes();
        $result     = [];
        foreach ($attributes as $attribute) {
            $result[$attribute->getAttributeCode()] = $attribute->getAttributeCode();
        }
        return $result;
    }

    /**
     * Get image file path
     *
     * @param string $imgFile
     * @return string
     */
    public function getImageFilePath($imgFile)
    {
        $moduleReader = ObjectManager::getInstance()->create('Magento\Framework\Module\Dir\Reader');
        $fileInfo = explode("/", $imgFile);
        $moduleName = $fileInfo[0];
        unset($fileInfo[0]);
        $fileInfo = implode("/", $fileInfo);
        $viewDir = $moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
            $moduleName
        );
        return $viewDir . '/base/web/'.$fileInfo;
    }
}

<?php

namespace Vnecoms\PdfPro\Helper;

use Magento\Framework\App\Helper\Context as Context;
use Magento\Store\Model\Store;
use Vnecoms\PdfPro\Model\KeyFactory;

/**
 * Class Data.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Configuration paths.
     */
    const PDF_DIR = 'lib/Vnecoms/mpdf';

    const IS_TYPE_VARIABLE = 1;

    const DISPLAY_SUBTOTAL_INCL_TAX = 2;
    const DISPLAY_SUBTOTAL_EXCL_TAX = 1;
    const DISPLAY_SUBTOTAL_BOTH = 3;

    const DISPLAY_SHIPPING_EXCL_TAX = 1;
    const DISPLAY_SHIPPING_INCL_TAX = 2;
    const DISPLAY_SHIPPING_BOTH = 3;

    const DISPLAY_PRICE_EXCL_TAX = 1;
    const DISPLAY_PRICE_INCL_TAX = 2;
    const DISPLAY_PRICE_BOTH = 3;

    const DISPLAY_GRANDTOTAL = 1;

    const XML_PATH_PDF_ENABLED = 'pdfpro/general/enabled';
    const XML_PATH_DEFAULT_API_KEY = 'pdfpro/general/default_key';
    const XML_PATH_PDF_TITLE = 'pdfpro/general/pdf_title';
    const XML_PATH_PROCESSOR_CONFIG = 'pdfpro/general/processor';
    const XML_PATH_REMOVE_DEFAULT_PRINT = 'pdfpro/general/remove_default_print';
    const XML_PATH_ADMIN_PRINT_ORDER = 'pdfpro/general/admin_print_order';
    const XML_PATH_ALLOW_CUSTOMER_PRINT = 'pdfpro/general/allow_customer_print';

    const XML_PATH_ORDER_EMAIL_ATTACK = 'pdfpro/general/order_email_attach';
    const XML_PATH_INVOICE_EMAIL_ATTACK = 'pdfpro/general/invoice_email_attach';
    const XML_PATH_SHIPMENT_EMAIL_ATTACK = 'pdfpro/general/shipment_email_attach';
    const XML_PATH_CREDITMEMO_EMAIL_ATTACK = 'pdfpro/general/creditmemo_email_attach';
    const XML_PATH_DETECT_LANGUAGE = 'pdfpro/general/detect_language';
    const XML_PATH_NUMBER_FORMAT = 'pdfpro/general/number_format';

    const XML_PATH_CURRENCY_POSITION = 'pdfpro/general/currency_position';
    const XML_PATH_METHOD = 'pdfpro/general/communication_method';

    protected $_convertTable = array(
        '&amp;' => 'and',   '@' => 'at',    '©' => 'c', '®' => 'r', 'À' => 'a',
        'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'Å' => 'a', 'Æ' => 'ae', 'Ç' => 'c',
        'È' => 'e', 'É' => 'e', 'Ë' => 'e', 'Ì' => 'i', 'Í' => 'i', 'Î' => 'i',
        'Ï' => 'i', 'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Õ' => 'o', 'Ö' => 'o',
        'Ø' => 'o', 'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'Ý' => 'y',
        'ß' => 'ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'å' => 'a',
        'æ' => 'ae', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ò' => 'o', 'ó' => 'o',
        'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u',
        'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'þ' => 'p', 'ÿ' => 'y', 'Ā' => 'a',
        'ā' => 'a', 'Ă' => 'a', 'ă' => 'a', 'Ą' => 'a', 'ą' => 'a', 'Ć' => 'c',
        'ć' => 'c', 'Ĉ' => 'c', 'ĉ' => 'c', 'Ċ' => 'c', 'ċ' => 'c', 'Č' => 'c',
        'č' => 'c', 'Ď' => 'd', 'ď' => 'd', 'Đ' => 'd', 'đ' => 'd', 'Ē' => 'e',
        'ē' => 'e', 'Ĕ' => 'e', 'ĕ' => 'e', 'Ė' => 'e', 'ė' => 'e', 'Ę' => 'e',
        'ę' => 'e', 'Ě' => 'e', 'ě' => 'e', 'Ĝ' => 'g', 'ĝ' => 'g', 'Ğ' => 'g',
        'ğ' => 'g', 'Ġ' => 'g', 'ġ' => 'g', 'Ģ' => 'g', 'ģ' => 'g', 'Ĥ' => 'h',
        'ĥ' => 'h', 'Ħ' => 'h', 'ħ' => 'h', 'Ĩ' => 'i', 'ĩ' => 'i', 'Ī' => 'i',
        'ī' => 'i', 'Ĭ' => 'i', 'ĭ' => 'i', 'Į' => 'i', 'į' => 'i', 'İ' => 'i',
        'ı' => 'i', 'Ĳ' => 'ij', 'ĳ' => 'ij', 'Ĵ' => 'j', 'ĵ' => 'j', 'Ķ' => 'k',
        'ķ' => 'k', 'ĸ' => 'k', 'Ĺ' => 'l', 'ĺ' => 'l', 'Ļ' => 'l', 'ļ' => 'l',
        'Ľ' => 'l', 'ľ' => 'l', 'Ŀ' => 'l', 'ŀ' => 'l', 'Ł' => 'l', 'ł' => 'l',
        'Ń' => 'n', 'ń' => 'n', 'Ņ' => 'n', 'ņ' => 'n', 'Ň' => 'n', 'ň' => 'n',
        'ŉ' => 'n', 'Ŋ' => 'n', 'ŋ' => 'n', 'Ō' => 'o', 'ō' => 'o', 'Ŏ' => 'o',
        'ŏ' => 'o', 'Ő' => 'o', 'ő' => 'o', 'Œ' => 'oe', 'œ' => 'oe', 'Ŕ' => 'r',
        'ŕ' => 'r', 'Ŗ' => 'r', 'ŗ' => 'r', 'Ř' => 'r', 'ř' => 'r', 'Ś' => 's',
        'ś' => 's', 'Ŝ' => 's', 'ŝ' => 's', 'Ş' => 's', 'ş' => 's', 'Š' => 's',
        'š' => 's', 'Ţ' => 't', 'ţ' => 't', 'Ť' => 't', 'ť' => 't', 'Ŧ' => 't',
        'ŧ' => 't', 'Ũ' => 'u', 'ũ' => 'u', 'Ū' => 'u', 'ū' => 'u', 'Ŭ' => 'u',
        'ŭ' => 'u', 'Ů' => 'u', 'ů' => 'u', 'Ű' => 'u', 'ű' => 'u', 'Ų' => 'u',
        'ų' => 'u', 'Ŵ' => 'w', 'ŵ' => 'w', 'Ŷ' => 'y', 'ŷ' => 'y', 'Ÿ' => 'y',
        'Ź' => 'z', 'ź' => 'z', 'Ż' => 'z', 'ż' => 'z', 'Ž' => 'z', 'ž' => 'z',
        'ſ' => 'z', 'Ə' => 'e', 'ƒ' => 'f', 'Ơ' => 'o', 'ơ' => 'o', 'Ư' => 'u',
        'ư' => 'u', 'Ǎ' => 'a', 'ǎ' => 'a', 'Ǐ' => 'i', 'ǐ' => 'i', 'Ǒ' => 'o',
        'ǒ' => 'o', 'Ǔ' => 'u', 'ǔ' => 'u', 'Ǖ' => 'u', 'ǖ' => 'u', 'Ǘ' => 'u',
        'ǘ' => 'u', 'Ǚ' => 'u', 'ǚ' => 'u', 'Ǜ' => 'u', 'ǜ' => 'u', 'Ǻ' => 'a',
        'ǻ' => 'a', 'Ǽ' => 'ae', 'ǽ' => 'ae', 'Ǿ' => 'o', 'ǿ' => 'o', 'ə' => 'e',
        'Ё' => 'jo', 'Є' => 'e', 'І' => 'i', 'Ї' => 'i', 'А' => 'a', 'Б' => 'b',
        'В' => 'v', 'Г' => 'g', 'Д' => 'd', 'Е' => 'e', 'Ж' => 'zh', 'З' => 'z',
        'И' => 'i', 'Й' => 'j', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n',
        'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't', 'У' => 'u',
        'Ф' => 'f', 'Х' => 'h', 'Ц' => 'c', 'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'sch',
        'Ъ' => '-', 'Ы' => 'y', 'Ь' => '-', 'Э' => 'je', 'Ю' => 'ju', 'Я' => 'ja',
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l',
        'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
        'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '-', 'ы' => 'y', 'ь' => '-', 'э' => 'je',
        'ю' => 'ju', 'я' => 'ja', 'ё' => 'jo', 'є' => 'e', 'і' => 'i', 'ї' => 'i',
        'Ґ' => 'g', 'ґ' => 'g', 'א' => 'a', 'ב' => 'b', 'ג' => 'g', 'ד' => 'd',
        'ה' => 'h', 'ו' => 'v', 'ז' => 'z', 'ח' => 'h', 'ט' => 't', 'י' => 'i',
        'ך' => 'k', 'כ' => 'k', 'ל' => 'l', 'ם' => 'm', 'מ' => 'm', 'ן' => 'n',
        'נ' => 'n', 'ס' => 's', 'ע' => 'e', 'ף' => 'p', 'פ' => 'p', 'ץ' => 'C',
        'צ' => 'c', 'ק' => 'q', 'ר' => 'r', 'ש' => 'w', 'ת' => 't', '™' => 'tm',
    );

    /** @var \Magento\Framework\Locale\CurrencyInterface */
    protected $_currency;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $__dateTime;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;
    /**
     * @var \Vnecoms\PdfPro\Model\PdfPro
     */
    protected $pdfPro;

    /**
     * @var \Vnecoms\PdfPro\Model\KeyFactory
     */
    protected $keyFactory;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Framework\Module\ResourceInterface
     */
    protected $moduleResource;

    /**
     * @var \Magento\Framework\View\Asset\Source
     */
    protected $assetSource;

    public function __construct(
        Context $context,
        KeyFactory $keyFactory,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Locale\CurrencyInterface $currency,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Backend\Model\UrlInterface $urlInterface,
        \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\View\Asset\Repository $repository,
        \Magento\Framework\Module\ResourceInterface $moduleResource,
        \Magento\Framework\View\Asset\Source $assetSource
    ) {
        $this->keyFactory = $keyFactory;
        $this->_dateTime = $dateTime;
        $this->_localeDate = $localeDate;
        $this->_currency = $currency;
        $this->_backendUrl = $urlInterface;
        $this->_storeManager = $storeManagerInterface;
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->fileSystem = $fileSystem;
        $this->assetRepo = $repository;
        $this->assetSource = $assetSource;
        $this->moduleResource = $moduleResource;
        parent::__construct($context);
    }

    /**
     * Init Pdf by given invoice data.
     *
     * @param array  $datas
     * @param string $type
     */
    public function initPdf($datas = array(), $type = 'invoice')
    {
        $processorConfig = $this->scopeConfig->getValue(self::XML_PATH_PROCESSOR_CONFIG, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        /*
         * @var \Vnecoms\AdvancedPdfProcessor\Model\Processor
         */
        $processor = \Magento\Framework\App\ObjectManager::getInstance()
            ->create($processorConfig);
        $apiKey = $this->getDefaultApiKey();

        return $processor->process($apiKey, $datas, $type);
    }

    /**
     * get enabled module or not.
     *
     * @param null|string|bool|int|Store $store
     *
     * @return bool
     */
    public function isEnableModule($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PDF_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * get store config.
     *
     * @param null   $store
     * @param string $path
     *
     * @return mixed
     */
    public function getConfig($path = '', $store = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * get remove default print config.
     *
     * @param null $store
     *
     * @return mixed
     */
    public function getRemoveDefaultPrint($store = null)
    {
        return $this->getConfig(self::XML_PATH_REMOVE_DEFAULT_PRINT, $store);
    }

    /**
     * get admin print order config.
     *
     * @param null $store
     *
     * @return mixed
     */
    public function getAdminPrintOrder($store = null)
    {
        return $this->getConfig(self::XML_PATH_ADMIN_PRINT_ORDER, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getAllowCustomerPrint($store = null)
    {
        return $this->getConfig(self::XML_PATH_ALLOW_CUSTOMER_PRINT, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getOrderEmailAttachment($store = null)
    {
        return $this->getConfig(self::XML_PATH_ORDER_EMAIL_ATTACK, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getInvoiceEmailAttachment($store = null)
    {
        return $this->getConfig(self::XML_PATH_INVOICE_EMAIL_ATTACK, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getShipmentEmailAttachment($store = null)
    {
        return $this->getConfig(self::XML_PATH_SHIPMENT_EMAIL_ATTACK, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getCreditmemoEmailAttachment($store = null)
    {
        return $this->getConfig(self::XML_PATH_CREDITMEMO_EMAIL_ATTACK, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getDetectLanguage($store = null)
    {
        return $this->getConfig(self::XML_PATH_DETECT_LANGUAGE, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getNumberFormat($store = null)
    {
        return $this->getConfig(self::XML_PATH_NUMBER_FORMAT, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getCurrencyPosition($store = null)
    {
        return $this->getConfig(self::XML_PATH_CURRENCY_POSITION, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getMethod($store = null)
    {
        return $this->getConfig(self::XML_PATH_METHOD, $store);
    }

    /**
     * @param null $store
     *
     * @return bool|mixed
     */
    public function getDefaultApiKey($store = null)
    {
        $defaultApiKey = $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_API_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        /** @var \Vnecoms\PdfPro\Model\Key $key */
        $key = $this->keyFactory->create();
        if ($defaultApiKey) {
            $key->load($defaultApiKey);

            return $key->getData('api_key');
        }

        return false;
    }

    /**
     * Convert and format price value for given currency code.
     *
     * @param float|int   $value
     * @param string      $code;
     * @param null|string $baseCode
     * @param string      $store
     *
     * @return string
     */
    public function currency($value, $code = 'USD', $baseCode = null, $store = null)
    {
        $precision = $this->scopeConfig->getValue(self::XML_PATH_NUMBER_FORMAT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store) !== '' ? $this->scopeConfig->getValue(self::XML_PATH_NUMBER_FORMAT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store) : 2;
        $value = $value ? $value : 0;
        $position = intval($this->scopeConfig->getValue(self::XML_PATH_CURRENCY_POSITION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store));
        $position ? $position : \Zend_Currency::STANDARD;

        return $this->_currency->getCurrency($code)->toCurrency($value, array('precision' => $precision, 'position' => $position));
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        $version = $this->moduleResource->getDbVersion('Vnecoms_PdfPro');
        return $version;
    }

    /**
     * @param $storeId
     * @param $groupId
     *
     * @return bool|mixed
     */
    public function getApiKey($storeId, $groupId)
    {
        $keyCollection = $this->keyFactory->create()->getCollection();
        $keyCollection->getSelect()->where("FIND_IN_SET('".$storeId."', store_ids) OR FIND_IN_SET('0', store_ids)")
            ->where("FIND_IN_SET('".$groupId."', customer_group_ids)")
            ->order('priority ASC');

        $apiKey = $keyCollection->count() ? $keyCollection->getFirstItem()->getApiKey() : $this->getDefaultApiKey();
        $apiKeyObj = new \Magento\Framework\DataObject(array('api_key' => $apiKey, 'store_id' => $storeId, 'group_id' => $groupId));
        $this->_eventManager->dispatch('ves_pdfpro_apikey_prepare', ['obj' => $apiKeyObj]);
        $apiKey = $apiKeyObj->getApiKey();

        return $apiKey;
    }

    /**
     * @param string $type
     * @param bool   $model
     * @param null   $store
     *
     * @return mixed
     */
    public function getFileName($type = 'invoice', $model = false, $store = null)
    {
        $fileName = $this->scopeConfig->getValue(
            'pdfpro/filename_format/'.$type,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        $dateTimeFormatArr = ['$d', '$M', '$y', '$H', '$m', '$s'];
        $timestamp = $this->_localeDate->scopeTimeStamp($store);
        foreach ($dateTimeFormatArr as $dateTimeFormat) {
            if ($dateTimeFormat == '$yy') {
                $fileName = str_replace($dateTimeFormat, $this->_localeDate->date($timestamp)
                    ->format('yy'), $fileName);
            }
            if ($dateTimeFormat == '$y') {
                $fileName = str_replace($dateTimeFormat, $this->_localeDate->date($timestamp)
                    ->format('y'), $fileName);
            }
            $fileName = str_replace($dateTimeFormat, $this->_localeDate->date($timestamp)
                ->format(trim($dateTimeFormat, '$')), $fileName);
        }
        if ($model) {
            $fileName = str_replace('$ID', $model->getIncrementId(), $fileName);
        }

        return $fileName;
    }

    /**
     * get pdf title configuration.
     *
     * @param null|string|bool|int|Store $store
     * @return string
     */
    public function getPdfTitleConfig($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PDF_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * get Base Url Media.
     *
     * @param string $path   [description]
     * @param bool   $secure [description]
     *
     * @return string [description]
     */
    public function getBaseUrlMedia($path = '', $secure = false)
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA, $secure).$path;
    }

    /**
     * @param string $route
     * @param array  $params
     *
     * @return string
     */
    public function getBackendUrl($route = '', $params = ['_current' => true])
    {
        return $this->_backendUrl->getUrl($route, $params);
    }

    /**
     * @param string $path
     * @param bool   $secure
     *
     * @return string
     */
    public function getBaseUrl($path = '', $secure = false)
    {
        return $this->_storeManager->getStore()->getBaseUrl().$path;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getBaseDirMedia($path = '')
    {
        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->fileSystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $path = $mediaDirectory->getAbsolutePath($path);

        return $path;
    }

    /**
     * @param $date
     * @param string $type
     *
     * @return \Magento\Framework\DataObject|mixed
     */
    public function getFormatedDate($date, $type = '')
    {
        $formats = $this->getDateFormat();
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        $dates = new \Magento\Framework\DataObject(
            [
                'full' => $this->dateTimeFormatter->formatObject($date, $formats->getData('full')),
                'long' => $this->dateTimeFormatter->formatObject($date, $formats->getData('long')),
                'medium' => $this->dateTimeFormatter->formatObject($date, $formats->getData('medium')),
                'short' => $this->dateTimeFormatter->formatObject($date, $formats->getData('short')),
            ]
        );

        if ($type) {
            return $dates->getData($type);
        }

        return $dates;
    }

    /**
     * @param null|string $time
     * @param string $type
     *
     * @return \Magento\Framework\DataObject|mixed
     */
    public function getFormatedTime($time, $type = '', $format = \IntlDateFormatter::SHORT, $showDate = false)
    {
        $time = $time instanceof \DateTimeInterface ? $time : new \DateTime($time);
        $timeFormat = 3;
        $timeFormats = [
            'full' => \IntlDateFormatter::FULL,
            'long' => \IntlDateFormatter::LONG,
            'medium' => \IntlDateFormatter::MEDIUM,
            'short' => \IntlDateFormatter::SHORT,
        ];
        if ($type) {
            $timeFormat =  $timeFormats[$type];
        }
        return $this->_localeDate->formatDateTime(
            $time,
            $showDate ? $format : \IntlDateFormatter::NONE,
            $timeFormat,
            null,
            null
        );
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getDateFormat()
    {
        $dateFormated = new \Magento\Framework\DataObject(
            array(
                'full' => $this->_localeDate->getDateFormat(\IntlDateFormatter::FULL),
                'long' => $this->_localeDate->getDateFormat(\IntlDateFormatter::LONG),
                'medium' => $this->_localeDate->getDateFormat(\IntlDateFormatter::MEDIUM),
                'short' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
            )
        );

        return $dateFormated;
    }

    public function getConvertTable()
    {
        return $this->_convertTable;
    }

    /**
     * format string.
     *
     * @param $str
     *
     * @return mixed|string
     */
    public function formatKey($str)
    {
        $str = strtr($str, $this->getConvertTable());
        $str = preg_replace('#[^0-9a-z]+#i', '', $str);
        $str = strtolower($str);
        $str = trim($str);

        return $str;
    }

    /**
     * get TCPDF DIR LIB.
     *
     * @return string
     */
    public function getPdfLibDir()
    {
        return true;
        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->fileSystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        $path = $mediaDirectory->getAbsolutePath(self::PDF_DIR);

        return $path;
    }

    public function getQrcodeLibDir()
    {
        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->fileSystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::APP);
        $path = $mediaDirectory->getAbsolutePath('code/Vnecoms/PdfPro/Qrcode');

        return $path;
    }

    public function getTaxDisplayConfig()
    {
        $data = $this->getConfig('tax/sales_display');
        $result = array();
        //for shipping
        if ($data['shipping'] == self::DISPLAY_SHIPPING_EXCL_TAX) {
            $result['display_shipping_excl_tax'] = true;
        } elseif ($data['shipping'] == self::DISPLAY_SHIPPING_INCL_TAX) {
            $result['display_shipping_incl_tax'] = true;
        } elseif ($data['shipping'] == self::DISPLAY_SHIPPING_BOTH) {
            $result['display_shipping_both'] = true;
        }

        //for subtotal
        if ($data['subtotal'] == self::DISPLAY_SUBTOTAL_EXCL_TAX) {
            $result['display_subtotal_excl_tax'] = true;
        } elseif ($data['subtotal'] == self::DISPLAY_SUBTOTAL_INCL_TAX) {
            $result['display_subtotal_incl_tax'] = true;
        } elseif ($data['subtotal'] == self::DISPLAY_SHIPPING_BOTH) {
            $result['display_subtotal_both'] = true;
        }

        //for grandtotal
        if ($data['grandtotal'] == self::DISPLAY_GRANDTOTAL) {
            $result['display_tax_in_grandtotal'] = true;
        }

        return $result;
    }

    public function processConstruction($construction)
    {
        $value = isset($construction[2]) ? $construction[2] : false;
        //remove whitespace
        $value = trim($value);
        $data1 = explode(' ', $value);
        foreach ($data1 as $_element) {
            $data2 = explode('=', $_element);
            $data2[1] = trim($data2[1], '"');
            $result[$data2[0]] = $data2[1];
        }
        $column = $result['column'];
        $column = unserialize(base64_decode($column));

        //sort by sort order in column
        usort($column, [$this, 'vesUsortHandle']);

        $result['column'] = $column;

        return $result;
    }

    public function vesUsortHandle($a, $b)
    {
        return $a['sortorder'] - $b['sortorder'];
    }

    /**
     * get Base DIR Web: pub/static.
     *
     * @param string $filename
     *
     * @return string
     */
    public function getBaseDirWeb($filename = '')
    {
        $staticDir = $this->fileSystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::STATIC_VIEW);
        $path = $staticDir->getAbsolutePath('');

        return $path.$this->assetRepo->createAsset('Vnecoms_PdfPro::'.$filename)->getPath();
    }

    public function getDefaultCss()
    {
        $staticDir = $this->fileSystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::STATIC_VIEW);
        $path = $staticDir->getAbsolutePath('');

        return $path.$this->assetRepo->createAsset('Vnecoms_PdfPro::templates/default.css')->getUrl();
    }

    /**
     * Return Custom Widget placeholders images URL.
     *
     * @return string
     */
    public function getCustomPlaceholderImagesBaseUrl()
    {
        return $this->assetRepo->getUrl('Vnecoms_PdfPro::images').'/';
    }

    /**
     * Return Custom Widget placeholders images dir.
     *
     * @return string
     */
    public function getCustomPlaceholderImagesBaseDir()
    {
        return $this->getBaseDirWeb('images');
        //return 'F:\xampp\htdocs\magento2\rl101\app\code\VnEcoms\AdvancedPdfProcessor\view\adminhtml\web\images';
    }

    public function getCustomAvailablePlaceholderFilenames()
    {
        // $result = array();

        return array('barcode.png', 'logo.png', 'logo_bg.gif', 'nolines_minus.gif', 'nolines_plus.gif',
            'widget.png', );
//        $targetDir = $this->getCustomPlaceholderImagesBaseDir().'/';
//
//        if (is_dir($targetDir) && is_readable($targetDir)) {
//            /** @var \Magento\Cms\Model\Wysiwyg\Images\Storage\Collection $collection */
//            $collection = $this->_storageCollectionFactory->create();
//            $collection->addTargetDir($targetDir)
//                ->setCollectDirs(false)
//                ->setCollectFiles(true)
//                ->setCollectRecursively(false);
//            foreach ($collection as $file) {
//                $result[] = $file;//->getBasename();
//            }
//        }
//
//        return $result;
    }

    /**
     * get custom images file name for widget.
     *
     * @return string
     */
    public function getCustomImageFileName()
    {
        return 'widget.png';
    }

    public function isTypeVariable()
    {
        return self::IS_TYPE_VARIABLE;
    }

    /**
     * get place holder image url (for widget in tinyMCE).
     *
     * @return string
     */
    public function getPlaceholderImageUrl()
    {
        $image = 'Vnecoms_PdfPro::images/'.$this->getCustomImageFileName();
        if ($image) {
            $asset = $this->assetRepo->createAsset($image);
            $placeholder = $this->assetSource->getFile($asset);
            if ($placeholder) {
                return $asset->getUrl();
            }
        }

        return $this->assetRepo->getUrl('Vnecoms_PdfPro::images/widget.png');
    }

    public function displayTaxCalculation(\Magento\Framework\DataObject $item)
    {
        if ($item->getTaxPercent() && $item->getTaxString() == '') {
            $percents = [$item->getTaxPercent()];
        } elseif ($item->getTaxString()) {
            $percents = explode(\Magento\Tax\Model\Config::CALCULATION_STRING_SEPARATOR, $item->getTaxString());
        } else {
            return '0%';
        }

        foreach ($percents as &$percent) {
            $percent = sprintf('%.2f%%', $percent);
        }
        return implode(' + ', $percents);
    }

    /**
     * Retrieve tax with percent html content
     *
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function displayTaxPercent(\Magento\Framework\DataObject $item)
    {
        if ($item->getTaxPercent()) {
            return sprintf('%s%%', $item->getTaxPercent() + 0);
        } else {
            return '0%';
        }
    }
}

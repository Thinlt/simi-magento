<?php
/**
 * Copyright © 2017 Vnecoms. All rights reserved.
 */

namespace Vnecoms\PdfPro\Model;

use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class AbstractPdf.
 *
 * @author Vnecoms team <vnecoms.com>
 */
abstract class AbstractPdf extends \Magento\Framework\DataObject
{
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    protected $_optionCollection;

    /**
     * @var \Magento\Framework\Locale\Resolver
     */
    protected $_locale;

    /**
     * @var DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * @var ListsInterface
     */
    protected $listInterface;

    /**
     * System event manager.
     *
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var         \Magento\Store\Model\App\Emulation $emulation
     */
    protected $emulation;


    /**
     * AbstractPdf constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface                $localeDate
     * @param \Vnecoms\PdfPro\Helper\Data                                         $helper
     * @param ListsInterface                                                      $listsInterface
     * @param ManagerInterface                                                    $event
     * @param \Magento\Framework\Locale\Resolver                                  $locale
     * @param \Magento\Store\Model\StoreManagerInterface                          $storeManagerInterface
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $option
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface       $dateTimeFormatter
     * @param array                                                               $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Vnecoms\PdfPro\Helper\Data $helper,
        ListsInterface $listsInterface,
        ManagerInterface $event,
        \Magento\Framework\Locale\Resolver $locale,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $option,
        \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter,
        \Magento\Store\Model\App\Emulation $emulation,
        array $data = []
    ) {
        $this->_locale = $locale;
        $this->_eventManager = $event;
        $this->_localeDate = $localeDate;
        $this->helper = $helper;
        $this->listInterface = $listsInterface;
        $this->_storeManager = $storeManagerInterface;
        $this->_optionCollection = $option;
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->emulation = $emulation;

        parent::__construct($data);
    }

    /**
     * @return array
     */
    public function getPriceAttributes()
    {
        return array();
    }

    /**
     * @return array
     */
    public function getBasePriceAttributes()
    {
        return [];
    }

    /**
     * process data.
     *
     * @param $sourceData
     * @param $currencyCode
     * @param null $baseCurrencyCode
     *
     * @return mixed
     */
    public function process($sourceData, $currencyCode, $baseCurrencyCode = null)
    {
        $baseCurrencyCode = $baseCurrencyCode ? $baseCurrencyCode : $this->_storeManager->getStore()->getBaseCurrency()->getCode();
        foreach ($sourceData as $key => $value) {
            if (is_object($value)) {
                unset($sourceData[$key]);
                continue;
            }
            if (in_array($key, $this->getPriceAttributes())) {
                if ($value) {
                    $sourceData[$key] = $this->helper->currency($value, $currencyCode);
                }
            }

            if (in_array($key, $this->getBasePriceAttributes())) {
                if ($value) {
                    $sourceData[$key] = $this->helper->currency($value, $baseCurrencyCode);
                }
            }
        }

        return $sourceData;
    }

    /**
     * @param $date
     * @param string $type
     *
     * @return \Magento\Framework\DataObject|mixed
     */
    public function getFormatedDate($date, $type = '', $showTime = true)
    {
        $formats = $this->getDateFormat();
        $date = new \DateTime($date);
        $dates = new \Magento\Framework\DataObject(
            [
                'full' => $this->_localeDate->formatDate($date, $formats->getData('full'), $showTime),
                'long' => $this->_localeDate->formatDate($date, $formats->getData('long'), $showTime),
                'medium' => $this->_localeDate->formatDate($date, $formats->getData('medium'), $showTime),
                'short' => $this->_localeDate->formatDate($date, $formats->getData('short'), $showTime),
            ]
        );

        if ($type) {
            return $dates->getData($type);
        }

        return $dates;
    }

    /**
     * return array of date formatted.
     * 
     * @return \Magento\Framework\DataObject
     */
    public function getDateFormat()
    {
        $dateFormated = new \Magento\Framework\DataObject(
            array(
                'full' => \IntlDateFormatter::FULL,
                'long' => \IntlDateFormatter::LONG,
                'medium' => \IntlDateFormatter::MEDIUM,
                'short' => \IntlDateFormatter::SHORT,
            )
        );
     //   if($type) return $dateFormated->getData($type);
        return $dateFormated;
    }

    /**
     * Get address data by address object.
     *
     * @param object $address
     *
     * @return \Magento\Framework\DataObject
     */
    public function getAddressData($address)
    {
        if (!$address) {
            return array();
        }
        $data = $address->getData();
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                unset($data[$key]);
            }
        }
        $data['country_name'] = $this->listInterface->getCountryTranslation($data['country_id']);
        $addressFormat = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magento\Sales\Model\Order\Address\Renderer');
        $data['formated'] = $addressFormat->format($address, 'html'); //var_dump($data['formated']);die();

        $data = new \Magento\Framework\DataObject($data);

        $this->_eventManager->dispatch('ves_pdfpro_data_prepare_after', ['source' => $data, 'model' => $address, 'type' => 'address']);

        return $data;
    }

    /**
     *
     */
    public function revertTranslation()
    {
        if (!$this->helper->getConfig('pdfpro/general/detect_language')) {
            return;
        }
        $this->emulation->stopEnvironmentEmulation();
    }

    /**
     * @param $storeId
     */
    public function setTranslationByStoreId($storeId)
    {
        if (!$this->helper->getConfig('pdfpro/general/detect_language', $storeId)) {
            return;
        }
        if ($storeId) {
            $this->emulation->startEnvironmentEmulation($storeId,'adminhtml');
        }
    }

    /**
     * @param $optionId
     *
     * @return \Magento\Framework\DataObject
     */
    public function getOptionById($optionId)
    {
        $option = $this->_optionCollection
            ->setOrder('sort_order', 'asc')
            ->addFieldToFilter('main_table.option_id', $optionId)
            ->setStoreFilter()
            ->load()
            ->getFirstItem();

        //var_dump($option->getData());die();
        return $option;
    }

    /**
     * get Customer Data from Customer Object.
     *
     * @param \Magento\Customer\Model\Customer $customer
     *
     * @return array
     */
    public function getCustomerData(\Magento\Customer\Model\Customer $customer)
    {
        if (!$customer->getId()) {
            return array('customer_is_guest' => 1);
        }
        $data = $customer->getData();
        if (isset($data['dob'])) {
            $data['customer_dob'] = $this->getFormatedDate($data['dob']);
        }
       // if(isset($data['gender'])) $data['gender']      = $this->getOptionById($data['gender'])->getData('value');
        $data = new \Magento\Framework\DataObject($data);

        $this->_eventManager->dispatch('ves_pdfpro_data_prepare_after', ['source' => $data, 'model' => $customer, 'type' => 'customer']);

        return $data->getData();
    }
}

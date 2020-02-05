<?php

namespace Vnecoms\PdfPro\Model\App;

use Magento\Framework\Translate\Inline\ConfigInterface;

/**
 * Class Emulation.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Emulation extends \Magento\Framework\DataObject
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\TranslateInterface
     */
    protected $_translate;

    /**
     * Core store config.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var \Magento\Framework\App\DesignInterface
     */
    protected $_design;

    /**
     * @var ConfigInterface
     */
    protected $inlineConfig;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * Ini.
     *
     * @var \Magento\Framework\DataObject
     */
    private $initialEnvironmentInfo;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Magento\Framework\View\DesignInterface            $viewDesign
     * @param \Magento\Framework\App\DesignInterface             $design
     * @param \Magento\Framework\TranslateInterface              $translate
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param ConfigInterface                                    $inlineConfig
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Locale\ResolverInterface        $localeResolver
     * @param \Psr\Log\LoggerInterface                           $logger
     * @param array                                              $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\DesignInterface $viewDesign,
        \Magento\Framework\App\DesignInterface $design,
        \Magento\Framework\TranslateInterface $translate,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ConfigInterface $inlineConfig,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        $this->_localeResolver = $localeResolver;
        parent::__construct($data);
        $this->_storeManager = $storeManager;
        $this->_viewDesign = $viewDesign;
        $this->_design = $design;
        $this->_translate = $translate;
        $this->_scopeConfig = $scopeConfig;
        $this->inlineConfig = $inlineConfig;
        $this->inlineTranslation = $inlineTranslation;
        $this->logger = $logger;
    }

    /**
     * Start enviromment emulation of the specified store.
     *
     * Function returns information about initial store environment and emulates environment of another store
     *
     * @param int    $storeId
     * @param string $area
     * @param bool   $emulateStoreInlineTranslation emulate inline translation of the specified store or just disable it
     *
     * @return \Magento\Framework\DataObject information about environment of the initial store
     */
    public function startEnvironmentEmulation($storeId, $area = \Magento\Framework\App\Area::AREA_FRONTEND, $emulateStoreInlineTranslation = false)
    {
        if (is_null($area)) {
            $area = \Magento\Framework\App\Area::AREA_FRONTEND;
        }
        if ($emulateStoreInlineTranslation) {
            $initialTranslateInline = $this->_emulateInlineTranslation($storeId, $area);
        } else {
            $initialTranslateInline = $this->_emulateInlineTranslation();
        }
        $initialDesign = $this->_emulateDesign($storeId, $area);
        // Current store needs to be changed right before locale change and after design change
        $this->_storeManager->setCurrentStore($storeId);
        $initialLocaleCode = $this->_emulateLocale($storeId, $area);

        $initialEnvironmentInfo = new Varien_Object();
        $initialEnvironmentInfo->setInitialTranslateInline($initialTranslateInline)
            ->setInitialDesign($initialDesign)
            ->setInitialLocaleCode($initialLocaleCode);

        return $initialEnvironmentInfo;
    }

    /**
     * Restore initial inline translation state.
     *
     * @param bool $initialTranslate
     *
     * @return $this
     */
    protected function _restoreInitialInlineTranslation($initialTranslate)
    {
        $this->inlineTranslation->resume($initialTranslate);

        return $this;
    }

    /**
     * Restore design of the initial store.
     *
     * @param array $initialDesign
     *
     * @return $this
     */
    protected function _restoreInitialDesign(array $initialDesign)
    {
        $this->_viewDesign->setDesignTheme($initialDesign['theme'], $initialDesign['area']);

        return $this;
    }

    /**
     * Restore locale of the initial store.
     *
     * @param string $initialLocaleCode
     * @param string $initialArea
     *
     * @return $this
     */
    protected function _restoreInitialLocale(
        $initialLocaleCode,
        $initialArea = \Magento\Framework\App\Area::AREA_ADMIN
    ) {
        $this->_localeResolver->setLocale($initialLocaleCode);
        $this->_translate->setLocale($initialLocaleCode);
        $this->_translate->loadData($initialArea);

        return $this;
    }
}

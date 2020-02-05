<?php

namespace Vnecoms\PdfPro\Helper;

use Magento\Framework\App\Helper\Context as Context;

/**
 * Class Mconfig.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Mconfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Data
     */
    protected $_helper;

    /**
     * Mconfig constructor.
     *
     * @param Data    $data
     * @param Context $context
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $data,
        Context $context
    ) {
        $this->_helper = $data;
        parent::__construct($context);
    }

    public function loadDefaultConfig()
    {
        $config = [];
        if (!is_dir($this->_helper->getBaseDirMedia('ves_pdfpro/tmp/'))) {
            mkdir($this->_helper->getBaseDirMedia('ves_pdfpro/tmp/'), 0777,  true);
          //  $config['tmp'] = $this->_helper->getBaseDirMedia('ves_pdfpro/tmp/');
        }
        if (!is_dir($this->_helper->getBaseDirMedia('ves_pdfpro/ttfontdata/'))) {
            mkdir($this->_helper->getBaseDirMedia('ves_pdfpro/ttfontdata/'), 0777,  true);
         //   $config['ttfontdata'] = $this->_helper->getBaseDirMedia('ves_pdfpro/ttfontdata/');
        }
        if (!is_dir($this->_helper->getBaseDirMedia('ves_pdfpro/graph_cache/'))) {
            mkdir($this->_helper->getBaseDirMedia('ves_pdfpro/graph_cache/'), 0777,  true);
         //   $config['graph_cache'] = $this->_helper->getBaseDirMedia('ves_pdfpro/graph_cache/');
        }

        $config['tmp'] = $this->_helper->getBaseDirMedia('ves_pdfpro/tmp/');
        $config['ttfontdata'] = $this->_helper->getBaseDirMedia('ves_pdfpro/ttfontdata/');
        $config['graph_cache'] = $this->_helper->getBaseDirMedia('ves_pdfpro/graph_cache/');

        return $config;
    }

    public function loadPdfConfig()
    {
        $config = $this->loadDefaultConfig();

        if (!defined('_MPDF_TEMP_PATH')) {
            define('_MPDF_TEMP_PATH', $config['tmp']);
        }

        if (!defined('_MPDF_TTFONTDATAPATH')) {
            define('_MPDF_TTFONTDATAPATH', $config['ttfontdata']);
        }
    }
}

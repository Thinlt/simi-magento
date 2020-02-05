<?php
namespace Vnecoms\Vendors\Block\Vendors\Widget\Form\Element;

class File extends \Vnecoms\Vendors\Block\Adminhtml\Form\Element\File
{
    /**
     * Adminhtml data
     *
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_url = null;

    /**
     *
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Vnecoms\Vendors\Helper\Data $vendorUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\Helper\Data $adminhtmlData,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Vnecoms\Vendors\Helper\Data $vendorUrl,
        $data = []
    ) {
        $this->_url = $vendorUrl;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $adminhtmlData, $assetRepo, $urlEncoder, $data);
        $this->setType('file');
    }

    /**
     * Return Preview/Download URL
     *
     * @return string
     */
    protected function _getPreviewUrl()
    {
        return $this->_url->getUrl(
            'account/index/viewfile',
            ['file' => $this->urlEncoder->encode($this->getValue())]
        );
    }
}

<?php

namespace Simi\Simistorelocator\Block\ListStore;

class SearchBox extends \Simi\Simistorelocator\Block\AbstractBlock {

    protected $_template = 'Simi_Simistorelocator::liststore/searchbox.phtml';

    /**
     * @var \Magento\Config\Model\Config\Source\Locale\Country
     */
    public $localCountry;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    public $directoryHelper;

    /**
     * Block constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Simi\Simistorelocator\Block\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Config\Model\Config\Source\Locale\Country $localCountry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->directoryHelper = $directoryHelper;
        $this->localCountry = $localCountry;
    }

    /**
     * @return string
     */
    public function getRegionJson() {
        return $this->directoryHelper->getRegionJson();
    }

    /**
     * get tag icon.
     *
     * @param \Simi\Simistorelocator\Model\Tag $tag
     *
     * @return string
     */
    public function getTagIcon(\Simi\Simistorelocator\Model\Tag $tag) {
        return $tag->getTagIcon() ? $this->imageHelper->getMediaUrlImage($tag->getTagIcon()) : $this->getViewFileUrl('Simi_Simistorelocator::images/Hospital_icon.png');
    }

    /**
     * @param \Simi\Simistorelocator\Model\Tag $tag
     *
     * @return string
     */
    public function getTagHtml(\Simi\Simistorelocator\Model\Tag $tag) {
        $tagFormat = '<li data-tag-id="%s" class="tag-icon icon-filter text-center">';
        $tagFormat .= '<img src="%s" class="img-responsive"/><p>%s</p></li>';

        return sprintf($tagFormat, $tag->getId(), $this->getTagIcon($tag), $tag->getTagName());
    }

    /**
     * @return array
     */
    public function getCountryOption() {
        return $this->localCountry->toOptionArray();
    }

}

<?php

namespace Simi\Simistorelocator\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Image extends \Simi\Simistorelocator\Ui\Component\Listing\Column\AbstractColumn
{
    /**
     * default width and height image.
     */
    const IMAGE_WIDTH = '70%';
    const IMAGE_HEIGHT = '60';
    const IMAGE_STYLE = 'display: block;margin: auto;';

    /**
     * @var \Simi\Simistorelocator\Helper\Image
     */
    public $imageHelper;

    /*
     * @var \Magento\Framework\View\Asset\Repository $assetRepo
     */
    protected $assetRepo;

    /**
     * Constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Simi\Simistorelocator\Helper\Image $imageHelper,
        array $components = [],
        array $data = [],
        \Magento\Framework\View\Asset\Repository $assetRepo
    )
    {
        {
            parent::__construct($context, $uiComponentFactory, $components, $data);
            $this->imageHelper = $imageHelper;
            $this->_assetRepo = $assetRepo;
        }
    }

    /**
     * prepare item.
     *
     * @param array $item
     *
     * @return array
     */
    protected
    function _prepareItem(array & $item)
    {
        $width = $this->hasData('width') ? $this->getWidth() : self::IMAGE_WIDTH;
        $height = $this->hasData('height') ? $this->getHeight() : self::IMAGE_HEIGHT;
        $style = $this->hasData('style') ? $this->getStyle() : self::IMAGE_STYLE;

//        If isset(item['baseimage']
        if (isset($item[$this->getData('name')])) {
            if ($item[$this->getData('name')]) {
                $srcImage = $this->imageHelper->getMediaUrlImage($item[$this->getData('name')]);
                $item[$this->getData('name')] = sprintf(
                    '<img src="%s"  width="%s" height="%s" style="%s" />',
                    $srcImage,
                    $width,
                    $height,
                    $style
                );
            }
        } else {
//        Show default store image in backend as base image but not save in database.
            $srcImage = $this->_assetRepo->getUrl("Simi_Simistorelocator::images/default_store.png");
            $item[$this->getData('name')] = sprintf(
                '<img src="%s"  width="%s" height="%s" style="%s" />',
                $srcImage,
                $width,
                $height,
                $style
            );
        }

        return $item;
    }

}

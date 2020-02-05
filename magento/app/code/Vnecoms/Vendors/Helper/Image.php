<?php
namespace Vnecoms\Vendors\Helper;

/**
 * Vendor image helper
 */
class Image extends \Magento\Catalog\Helper\Image
{
    /**
     * Image factory
     *
     * @var \Vnecoms\Vendors\Model\ImageFactory
     */
    protected $_imageFactory;

    /**
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\Product\ImageFactory $productImageFactory
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\ConfigInterface $viewConfig
     * @param \Vnecoms\Vendors\Model\ImageFactory $imageFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\ImageFactory $productImageFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Vnecoms\Vendors\Model\ImageFactory $imageFactory
    ) {
        $this->_imageFactory = $imageFactory;
        parent::__construct($context, $productImageFactory, $assetRepo, $viewConfig);
    }
    
    /**
     * Init image
     */
    public function init($imageFile, $notUsed = '', $attributes = [])
    {
        $this->_reset();
        $this->_imageFile = $imageFile;
        $this->attributes = $attributes;
        $this->setImageProperties();
        $this->setWatermarkProperties();
    
        return $this;
    }

    /**
     * Set base media path
     *
     * @param string $path
     * @return \Vnecoms\Vendors\Helper\Image
     */
    public function setBaseMediaPath($path)
    {
        $this->_getModel()->setData('base_media_path', $path);
        return $this;
    }

    /**
     * Initialize base image file
     *
     * @return $this
     */
    protected function initBaseFile()
    {
        $model = $this->_getModel();
        if (!$model->getBaseFile()) {
            $model->setBaseFile($this->getImageFile());
        }
        return $this;
    }
    
    /**
     * Get current Image model
     *
     * @return \Vnecoms\Vendors\Model\Image
     */
    protected function _getModel()
    {
        if (!$this->_model) {
            $this->_model = $this->_imageFactory->create();
        }
        return $this->_model;
    }
    
    /**
     * Retrieve image type
     *
     * @return string
     */
    public function getType()
    {
        return $this->getAttribute('type')?$this->getAttribute('type'):'image';
    }

    /**
     * Retrieve image frame flag
     *
     * @return false|string
     */
    public function getFrame()
    {
        $imageFrame = $this->getAttribute('frame');
        if (empty($imageFrame)) {
            $imageFrame = $this->getConfigView()->getVarValue('Vnecoms_Vendors', 'image_white_borders');
        }
        return $imageFrame;
    }

    /**
     * Return image label
     *
     * @return string
     */
    public function getLabel()
    {
        $label = $this->getData('label');
        return $label;
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Block\Vendors\Product\Edit;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class NewVideo extends \Magento\ProductVideo\Block\Adminhtml\Product\Edit\NewVideo
{
   
    /**
     * @var string
     */
    protected $_template = 'Vnecoms_Vendors::widget/form.phtml';
    
    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        \Magento\Framework\Data\Form::setElementRenderer(
            $this->getLayout()->createBlock(
                'Vnecoms\Vendors\Block\Vendors\Widget\Form\Renderer\Element',
                $this->getNameInLayout() . '_element'
            )
        );
        \Magento\Framework\Data\Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(
                'Vnecoms\Vendors\Block\Vendors\Widget\Form\Renderer\Fieldset',
                $this->getNameInLayout() . '_fieldset'
            )
        );
        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Vnecoms\Vendors\Block\Vendors\Widget\Form\Renderer\Fieldset\Element',
                $this->getNameInLayout() . '_fieldset_element'
            )
        );
    
        return $this;
    }

    /**
     * Get widget options
     *
     * @return string
     */
    public function getWidgetOptions()
    {
        return $this->jsonEncoder->encode(
            [
                'saveVideoUrl' => $this->getUrl('catalog/product_gallery/upload'),
                'saveRemoteVideoUrl' => $this->getUrl('catalog/product_gallery/retrieveImage'),
                'htmlId' => $this->getHtmlId(),
                'youTubeApiKey' => $this->mediaHelper->getYouTubeApiKey()
            ]
        );
    }

    /**
     * Get note for video url
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getNoteVideoUrl()
    {
        $result = __('YouTube and Vimeo supported.');
        if ($this->mediaHelper->getYouTubeApiKey() === null) {
            $result = __(
                'Vimeo and Youtube are supported.<br />'
            );
        }
        return $result;
    }
}

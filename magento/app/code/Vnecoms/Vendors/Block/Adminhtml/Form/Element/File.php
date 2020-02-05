<?php

namespace Vnecoms\Vendors\Block\Adminhtml\Form\Element;

class File extends \Magento\Customer\Block\Adminhtml\Form\Element\File
{
    /**
     * Return Delete CheckBox SPAN Class name
     *
     * @return string
     */
    protected function _getDeleteCheckboxSpanClass()
    {
        return $this->_isImage()?'delete-image':'delete-file';
    }

    /**
     * Return Delete CheckBox Label
     *
     * @return \Magento\Framework\Phrase
     */
    protected function _getDeleteCheckboxLabel()
    {
        return $this->_isImage()?__('Delete Image'):__('Delete File');
    }

    /**
     * Get file extension
     * @return string
     */
    protected function _getFileExtension()
    {
        if(!$this->getValue() || is_array($this->getValue())) return false;
        $extension = explode(".", $this->getValue());
        $extension = end($extension);
        $extension = strtolower($extension);
        
        return $extension;
    }
    
    /**
     * Is image file
     * @return boolean
     */
    protected function _isImage()
    {
        return in_array($this->_getFileExtension(), ['jpg','png','gif','bmp']);
    }
    
    /**
     * @return string
     */
    protected function getImagePreviewHtml()
    {
        $html = '';
        if ($this->getValue() && !is_array($this->getValue())) {
            $imgUrl = $this->_getPreviewUrl();
            $imgId = sprintf('%s_image', $this->getHtmlId());
            $image = [
                'alt' => __('View Full Size'),
                'title' => __('View Full Size'),
                'src' => $imgUrl,
                'class' => 'small-image-preview v-middle',
                'height' => 22,
                'width' => 22,
                'id' => $imgId
            ];
            $link = ['href' => $imgUrl, 'onclick' => "imagePreview('{$imgId}'); return false;"];
    
            $html = sprintf(
                '%s%s</a> ',
                $this->_drawElementHtml('a', $link, false),
                $this->_drawElementHtml('img', $image)
            );
        }
        return $html;
    }
    
    /**
     * Return File preview link HTML
     *
     * @return string
     */
    protected function _getPreviewHtml()
    {
        if ($this->_isImage()) {
            return $this->getImagePreviewHtml();
        }
        
        $htmlResult = '';
        if ($this->getValue() && !is_array($this->getValue())) {
            $image = [
                'alt' => __('Download file'),
                'title' => __('Download file'),
                'src'   => $this->_assetRepo->getUrl('images/fam_bullet_disk.gif'),
                'class' => 'v-middle'
            ];
            $previewUrl = $this->_getPreviewUrl();
            $htmlResult .= '<span>';
            $htmlResult .= '<a href="' . $previewUrl . '">' . $this->_drawElementHtml('img', $image) . '</a> ';
            $htmlResult .= '<a href="' . $previewUrl . '">' . __('Download') . '</a>';
            $htmlResult .= '</span>';
        }
        return $htmlResult;
    }

    /**
     * Return Preview/Download URL
     *
     * @return string
     */
    protected function _getPreviewUrl()
    {
        return $this->_adminhtmlData->getUrl(
            'vendors/index/viewfile',
            [
                'file' => $this->urlEncoder->encode($this->getValue()),
            ]
        );
    }
    
    /**
     * Return Form Element HTML
     *
     * @return string
     */
    public function getElementHtml(){
        $this->addClass('input-file');
        if ($this->getRequired()) {
            $this->removeClass('required-entry');
        }
        
        $element = sprintf(
            '<input id="%s" name="%s" %s />%s%s',
            $this->getHtmlId(),
            $this->getName(),
            $this->serialize($this->getHtmlAttributes()),
            $this->getAfterElementHtml(),
            $this->_getHiddenInput()
        );
        return $this->_getPreviewHtml() . $element . $this->_getDeleteCheckboxHtml();
    }
}

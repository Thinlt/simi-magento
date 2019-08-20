<?php
namespace Vnecoms\PageBuilder\Block\Field;

class Link extends \Vnecoms\PageBuilder\Block\Field\Text
{
    /**
     * Get link URL
     * 
     * @return string
     */
    public function getLinkUrl(){
        if($this->getData('linkType') == 'none') return '#';
        
        return $this->getHref();
    }
    
    /**
     * Is open new window
     * 
     * @retun boolean
     */
    public function isOpenNewWindow(){
        return $this->getData('openNewWindow');
    }
}

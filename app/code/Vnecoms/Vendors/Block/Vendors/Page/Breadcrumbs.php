<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Vendors\Page;

/**
 * Vendor Breadcrumbs block
 *
 */
class Breadcrumbs extends \Vnecoms\Vendors\Block\Vendors\AbstractBlock
{
    /**
     * Breadcrumbs links
     *
     * @var array
     */
    protected $_links = [];

    /**
     * @var string
     */
    protected $_template = 'Vnecoms_Vendors::page/breadcrumbs.phtml';

    /**
     * @return void
     */
    protected function _construct()
    {
        /* $this->addLink(__('Home'), __('Home'), $this->getUrl('*')); */
    }

    /**
     * @return string
     */
    public function getHomeLink()
    {
        return $this->getUrl($this->_urlBuilder->getStartupPageUrl());
    }
    
    /**
     * @param string $label
     * @param string|null $title
     * @param string|null $url
     * @return $this
     */
    public function addLink($label, $title = null, $url = null)
    {
        if (empty($title)) {
            $title = $label;
        }
        $this->_links[] = ['label' => $label, 'title' => $title, 'url' => $url];
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        // TODO - Moved to Beta 2, no breadcrumbs displaying in Beta 1
        $this->assign('links', $this->_links);
        return parent::_beforeToHtml();
    }
}

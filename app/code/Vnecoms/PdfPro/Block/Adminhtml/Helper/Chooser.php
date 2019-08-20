<?php

// @codingStandardsIgnoreFile

/**
 * Catalog product gallery attribute.
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Vnecoms\PdfPro\Block\Adminhtml\Helper;

use Magento\Framework\Registry;

class Chooser extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Gallery html id.
     *
     * @var string
     */
    protected $htmlId = 'templates';

    /**
     * Gallery name.
     *
     * @var string
     */
    protected $name = 'templates';

    /**
     * @var string
     */
    protected $formName = 'pdfpro_key_form';

    /**
     * @var \Magento\Framework\Data\Form
     */
    protected $form;

    /**
     * @var Registry
     */
    protected $registry;

    protected $content;

    /**
     * AssignProducts constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = $this->getContentHtml();

        return $html;
    }

    /**
     * Prepares content block.
     *
     * @return string
     */
    public function getContentHtml()
    {
        /* @var $content \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content */
        $content = $this->getChildBlock('content');
        $content->setId($this->getHtmlId().'_content')->setElement($this);
        $content->setFormName($this->formName);

        return $content->toHtml();

/*        if (null === $this->content) {
            $this->content = $this->getLayout()->createBlock(
                'Vnecoms\PdfPro\Block\Adminhtml\Helper\Chooser\Content',
                'pdfpro.chooser.content'
            );
        }
        return $this->content;*/
    }

    /**
     * @return string
     */
    protected function getHtmlId()
    {
        return $this->htmlId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getCurrentKey()
    {
        return $this->registry->registry('current_key');
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        return $this->getElementHtml();
    }
}

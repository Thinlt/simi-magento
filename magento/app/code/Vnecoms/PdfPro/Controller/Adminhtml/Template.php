<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Vnecoms\PdfPro\Model\TemplateFactory as TemplateFactory;
use Magento\Framework\Registry;

/**
 * Class Template.
 *
 * @author Vnecoms team <vnecoms.com>
 */
abstract class Template extends Action
{
    /**
     * template factory.
     *
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * Core registry.
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * date filter.
     *
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * File Factory.
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param Registry        $registry
     * @param TemplateFactory $templateFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param Context         $context
     */
    public function __construct(
        Registry $registry,
        TemplateFactory $templateFactory,
        //RedirectFactory $resultRedirectFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        Context $context

    ) {
        $this->coreRegistry = $registry;
        $this->templateFactory = $templateFactory;
        // $this->resultRedirectFactory = $resultRedirectFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->_fileFactory = $fileFactory;

        parent::__construct($context);
    }

    /**
     * @return \VnEcoms\AdvancedPdfProcessor\Model\Template
     */
    protected function initTemplate()
    {
        $templateId = (int) $this->getRequest()->getParam('id');
        /** @var \VnEcoms\AdvancedPdfProcessor\Model\Template $template */
        $template = $this->templateFactory->create();
        if ($templateId) {
            $template->load($templateId);
        }
        $this->coreRegistry->register('template', $template);
        $this->coreRegistry->register('current_template', $template);

        return $template;
    }

    /**
     * filter.
     *
     * @param array $data
     *
     * @return array
     */
    public function filterData($data)
    {
        return $data;
    }
}

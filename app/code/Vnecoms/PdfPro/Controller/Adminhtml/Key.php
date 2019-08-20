<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Vnecoms\PdfPro\Model\KeyFactory;
use Magento\Framework\Registry;

/**
 * Class Key.
 *
 * @author Vnecoms team <vnecoms.com>
 */
abstract class Key extends Action
{
    /**
     * key factory.
     *
     * @var KeyFactory
     */
    protected $keyFactory;

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
     * Key constructor.
     *
     * @param Registry                                         $registry
     * @param KeyFactory                                       $keyFactory
     * @param RedirectFactory                                  $resultRedirectFactory
     * @param Context                                          $context
     * @param \Magento\Framework\View\Result\LayoutFactory     $resultLayoutFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        Registry $registry,
        KeyFactory $keyFactory,
        //RedirectFactory $resultRedirectFactory,
        Context $context,

        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory

    ) {
        $this->coreRegistry = $registry;
        $this->keyFactory = $keyFactory;
        //$this->resultRedirectFactory = $resultRedirectFactory;
        $this->_fileFactory = $fileFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($context);
    }

    /**
     * @return \Vnecoms\PdfPro\Model\Key
     */
    protected function initKey()
    {
        $keyId = (int) $this->getRequest()->getParam('entity_id');
        /** @var \Vnecoms\PdfPro\Model\Key $key */
        $key = $this->keyFactory->create();
        if ($keyId) {
            $key->load($keyId);
        }
        $this->coreRegistry->register('key_data', $key);
        $this->coreRegistry->register('current_key', $key);

        return $key;
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

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::pdfpro_apikey');
    }
}

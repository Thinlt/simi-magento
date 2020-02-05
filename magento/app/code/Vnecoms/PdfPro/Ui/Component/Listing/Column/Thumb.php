<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Thumb extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'logo';

    const ALT_FIELD = 'name';

    protected $_helper;

    /**
     * @param ContextInterface                $context
     * @param UiComponentFactory              $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array                           $components
     * @param array                           $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Vnecoms\PdfPro\Helper\Data $helper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->_helper = $helper;
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('logo');
            foreach ($dataSource['data']['items'] as &$item) {
                $key = new \Magento\Framework\DataObject($item);
                $item[$fieldName.'_src'] = $this->_helper->getMediaUrl('ves_pdfpro/logos/'.$key->getData('logo'));
                $item[$fieldName.'_alt'] = $key->getData('api_key');
                $item[$fieldName.'_link'] = $this->urlBuilder->getUrl(
                    'vnecoms_pdfpro/key/edit',
                    ['id' => $key->getData('entity_id')]
                );
                $item[$fieldName.'_orig_src'] = $this->_helper->getMediaUrl('ves_pdfpro/logos/'.$key->getData('logo'));
            }
        }

        return $dataSource;
    }
}

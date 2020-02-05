<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Ui\Component\Listing\Column\Giftcard;

/**
 * Class Code
 *
 * @package Aheadworks\Giftcard\Ui\Component\Listing\Column
 */
class Code extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        foreach ($dataSource['data']['items'] as & $item) {
            $item[$fieldName . '_label'] = $item['code'];
            $item[$fieldName . '_url'] = $this->context->getUrl(
                'simivendor/giftcardcodes/edit',
                ['id' => $item['id']]
            );
        }

        return $dataSource;
    }
}

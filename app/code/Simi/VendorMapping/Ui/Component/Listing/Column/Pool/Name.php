<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Ui\Component\Listing\Column\Pool;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Name
 *
 * @package Aheadworks\Giftcard\Ui\Component\Listing\Column
 */
class Name extends Column
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        foreach ($dataSource['data']['items'] as & $item) {
            $item[$fieldName . '_label'] = $item['name'];
            $item[$fieldName . '_url'] = $this->context->getUrl(
                'simivendor/giftcardpools/edit',
                ['id' => $item['id']]
            );
        }

        return $dataSource;
    }
}

<?php

namespace Vnecoms\Vendors\Ui\Component\Listing\Column;

/**
 * Class ProductActions
 */
class Actions extends \Magento\Customer\Ui\Component\Listing\Column\Actions
{
    /**
     * Prepare Data Source
     *
     * @param array $dSource
     * @return array
     */
    public function prepareDataSource(array $dSource)
    {
        if (isset($dSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');

            foreach ($dSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'vendors/*/edit',
                        ['id' => $item['entity_id'], 'store' => $storeId]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
            }
        }

        return $dSource;
    }
}

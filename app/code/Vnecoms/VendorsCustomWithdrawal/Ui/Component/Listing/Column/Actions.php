<?php

namespace Vnecoms\VendorsCustomWithdrawal\Ui\Component\Listing\Column;

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
            foreach ($dSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'vendors/credit_withdrawal_method/edit',
                        ['id' => $item['method_id']]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
            }
        }

        return $dSource;
    }
}

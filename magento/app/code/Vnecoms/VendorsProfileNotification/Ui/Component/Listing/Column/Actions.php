<?php
namespace Vnecoms\VendorsProfileNotification\Ui\Component\Listing\Column;

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
            $indexField = $this->getData('config/indexField') ?: 'process_id';
            $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
            $urlEntityParamName = $this->getData('config/urlEntityParamName') ?: 'id';
            foreach ($dSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        $viewUrlPath,
                        [$urlEntityParamName => $item[$indexField]]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
            }
        }

        return $dSource;
    }
}

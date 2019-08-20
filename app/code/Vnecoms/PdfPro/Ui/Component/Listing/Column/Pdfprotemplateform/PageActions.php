<?php

namespace Vnecoms\PdfPro\Ui\Component\Listing\Column\Pdfprotemplateform;

class PageActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                $id = 'X';
                if (isset($item['pulsestorm_gridexample_log_id'])) {
                    $id = $item['pulsestorm_gridexample_log_id'];
                }
                $item[$name]['view'] = [
                    'href' => $this->getContext()->getUrl(
                        'adminhtml/pdfpro_template_form/viewlog', ['id' => $id]),
                    'label' => __('Edit'),
                ];
            }
        }

        return $dataSource;
    }
}

<?php

namespace VnEcoms\PdfPro\Block\Adminhtml\Key\Grid;

use Magento\Framework\Data\CollectionDataSourceInterface;
use VnEcoms\PdfPro\Model\Resource\Key\Collection;
use Magento\Backend\Block\Widget\Grid\Column;

class DataSource implements CollectionDataSourceInterface
{
    /**
     * filter by store.
     *
     * @param Collection $collection
     * @param Column     $column
     *
     * @return $this
     */
    public function filterStoreCondition(Collection $collection, Column $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $collection->addStoreFilter($value);

        return $this;
    }
}

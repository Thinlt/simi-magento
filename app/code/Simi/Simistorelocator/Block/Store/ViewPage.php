<?php

namespace Simi\Simistorelocator\Block\Store;

class ViewPage extends \Simi\Simistorelocator\Block\AbstractBlock {

    /**
     * Block constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
    \Simi\Simistorelocator\Block\Context $context, array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return \Simi\Simistorelocator\Model\Store
     */
    public function getStore() {
        return $this->coreRegistry->registry('simistorelocator_store');
    }

    /**
     * Get schedule time html in a day.
     *
     * $day can be 'monday', 'tuesday', ...
     *
     * @param \Simi\Simistorelocator\Model\Store $store
     * @param string                              $day
     */
    public function getScheduleTimeHtml(\Simi\Simistorelocator\Model\Store $store, $day) {
        if ($store->isOpenday($day)) {
            if ($store->hasBreakTime($day)) {
                return sprintf(
                        '<td>%s - %s && %s - %s</td>',
                        $store->getData($day . '_open'),
                        $store->getData($day . '_open_break'),
                        $store->getData($day . '_close_break'),
                        $store->getData($day . '_close')
                );
            } else {
                return sprintf(
                        '<td>%s - %s</td>',
                        $store->getData($day . '_open'),
                        $store->getData($day . '_close')
                );
            }
        } else {
            return '<td>' . __('Closed') . '</td>';
        }
    }

    /**
     * @return array
     */
    public function getHashWeekdays() {
        return [
            'sunday' => __('Sun'),
            'monday' => __('Mon'),
            'tuesday' => __('Tue'),
            'wednesday' => __('Wed'),
            'thursday' => __('Thur'),
            'friday' => __('Fri'),
            'saturday' => __('Sat'),
        ];
    }

    /**
     * Get current Url.
     *
     * @return string
     */
    public function getCurrentUrl() {
        return $this->_urlBuilder->getCurrentUrl();
    }

    public function getStoreJson(\Simi\Simistorelocator\Model\Store $store) {
        return $store->toJson([
                    'simistorelocator_id',
                    'latitude',
                    'longitude',
                    'zoom_level',
                    'marker_icon',
                    'baseimage',
                    'store_name',
                    'address',
                    'phone',
                    'email',
                    'fax',
                    'link',
        ]);
    }

}

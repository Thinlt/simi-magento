<?php

namespace Simi\Simistorelocator\Block\Adminhtml\Widget\Form\Element;

class Time extends \Magento\Framework\Data\Form\Element\Time {

    /**
     * get hours html.
     *
     * @param $value_hrs
     *
     * @return string
     */
    protected function _getHourHtml($value_hrs) {
        $html = '<select id="' . $this->getHtmlId() . '_hour" name="' . $this->getName() . '" style="width:80px" ';
        $html .= $this->serialize(
                        $this->getHtmlAttributes()
                ) . $this->_getUiId(
                        'hour'
                ) . '>' . "\n";
        for ($i = 0; $i < 24; ++$i) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $html .= '<option value="' . $hour . '" ' . ($value_hrs == $i ? 'selected="selected"' : '') . '>' . $hour . '</option>';
        }
        $html .= '</select>' . "\n";

        return $html;
    }

    /**
     * get minute html.
     *
     * @param $value_min
     *
     * @return string
     */
    protected function _getMinuteHtml($value_min) {
        $html = '<select id="' . $this->getHtmlId() . '_minute" name="' . $this->getName() . '" style="width:80px" ';
        $html .= $this->serialize(
                        $this->getHtmlAttributes()
                ) . $this->_getUiId(
                        'minute'
                ) . '>' . "\n";
        for ($i = 0; $i < 60; ++$i) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $html .= '<option value="' . $hour . '" ' . ($value_min == $i ? 'selected="selected"' : '') . '>' . $hour . '</option>';
        }
        $html .= '</select>' . "\n";

        return $html;
    }

    /**
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getElementHtml() {
        $this->addClass('select admin__control-select');

        $value_hrs = 0;
        $value_min = 0;

        if ($value = $this->getValue()) {
            $values = explode(':', $value);
            if (is_array($values) && count($values) == 2) {
                list($value_hrs, $value_min) = $values;
            }
        }

        $html = '<input type="hidden" id="' . $this->getHtmlId() . '" ' . $this->_getUiId() . '/>';
        $html .= $this->_getHourHtml($value_hrs);
        $html .= ':&nbsp;' . $this->_getMinuteHtml($value_min);
        $html .= $this->getAfterElementHtml();

        return $html;
    }
}

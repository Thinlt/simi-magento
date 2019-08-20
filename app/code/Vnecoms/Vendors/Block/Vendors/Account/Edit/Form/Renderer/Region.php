<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Vendors\Account\Edit\Form\Renderer;

/**
 * Customer address region field renderer
 */
class Region extends \Vnecoms\Vendors\Block\Adminhtml\Vendor\Edit\Renderer\Region
{
    /**
     * Output the region element and javasctipt that makes it dependent from country element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($country = $element->getForm()->getElement('country_id')) {
            $countryId = $country->getValue();
        } else {
            return $element->getDefaultHtml();
        }

        if ($element->getForm()->getElement('region_id')) {
            $regionId = $element->getForm()->getElement('region_id')->getValue();
        } else {
            $regionId = 0;
        }

     

        $html = '<div class="form-group field-state required admin__field _required">';
        $element->setClass('form-control input-text admin__control-text');
        $element->setRequired(true);
        $html .= '<label for="vendor_postcode" class="col-sm-3 control-label">'.$element->getLabel().'</label>';
        $html .= '<div class="col-sm-9 control admin__field-control">';
        $html .= $element->getElementHtml();

        $selectName = str_replace('region', 'region_id', $element->getName());
        $selectId = $element->getHtmlId() . '_id';
        $html .= '<select id="' .
            $selectId .
            '" name="' .
            $selectName .
            '" class="form-control select required-entry admin__control-select" style="display:none">';
        $html .= '<option value="">' . __('Please select') . '</option>';
        $html .= '</select>';

        $html .= '<script>' . "\n";
        $html .= 'require(["jquery","jquery/bootstrap","prototype", "mage/adminhtml/form"], function(jQuery){';
        $html .= '$("' . $selectId . '").setAttribute("defaultValue", "' . $regionId . '");' . "\n";
//         $html .= 'var disablePrototypeJS = function (method, pluginsToDisable) {
//                 var handler = function (event) {  
//                     event.target[method] = undefined;
//                     setTimeout(function () {
//                         delete event.target[method];
//                     }, 0);
//                 };
//                 pluginsToDisable.each(function (plugin) { 
//                     jQuery(window).on(method + \'.bs.\' + plugin, handler); 
//                 });
//             },
//             pluginsToDisable = [\'collapse\', \'dropdown\', \'modal\', \'tooltip\', \'popover\', \'tab\'];
//         disablePrototypeJS(\'show\', pluginsToDisable);
//         disablePrototypeJS(\'hide\', pluginsToDisable);';
                        
        $html .= 'new regionUpdater("' .
            $country->getHtmlId() .
            '", "' .
            $element->getHtmlId() .
            '", "' .
            $selectId .
            '", ' .
            $this->_directoryHelper->getRegionJson() .
            ');' .
            "\n";

        $html .= '});';
        $html .= '</script>' . "\n";

        $html .= '</div></div>' . "\n";

        return $html;
    }
}

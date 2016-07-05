<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Gmap Settings Block
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @author      Magestore Developer
 */
class Magestore_Loyalty_Block_Adminhtml_Field_Gmap extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * render config row
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id     = $element->getHtmlId();
        $label  = $element->getLabel();
        
        $html  = '<tr id="row_' . $id . '">'
                . '<td></td><td colspan="2" class="value">';
        $html .= '<button id="btn_' . $id . '" type="button" class="scalable" onclick="toggleMap()"><span><span>';
        $html .= $label;
        $html .= '</span></span></button>';
        $html .= '<script src="//maps.google.com/maps/api/js?sensor=true"></script>';
        $html .= '<div id="googleMap" style="display:none;height:400px;width:600px;margin-top:4px;border:gray 1px solid;"></div>';
        $html .= '<script type="text/javascript">
            var gMapBtn = $("btn_' . $id . '");
            var latitude = parseFloat($("rewardpoints_passbook_latitude").getValue());
            var longitude = parseFloat($("rewardpoints_passbook_longitude").getValue());
            
            var StoreMap = Class.create();
            StoreMap.prototype = {
                initialize: function(latitude, longtitude) {
                    this.stockholm = new google.maps.LatLng(latitude, longtitude);
                    this.marker = null;
                    this.map = null;
                },
                initGoogleMap: function() {
                    var mapOptions = {
                        zoom: 15,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        center: this.stockholm
                    };
                    this.map = new google.maps.Map($("googleMap"), mapOptions);
                    var pinImage = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|f75448";
                    this.marker = new google.maps.Marker({
                        map: this.map,
                        draggable: true,
                        position: this.stockholm,
                        icon: pinImage
                    });
                    google.maps.event.addListener(this.marker, "dragend", function(event) {
                        $("rewardpoints_passbook_latitude").value = event.latLng.lat();
                        $("rewardpoints_passbook_longitude").value = event.latLng.lng();
                        if ($("rewardpoints_passbook_latitude_inherit")) {
                            var el = $("rewardpoints_passbook_latitude_inherit");
                            if (el.checked) {
                                el.checked = false;
                                toggleValueElements(el, Element.previous(el.parentNode));
                            }
                            var el = $("rewardpoints_passbook_longitude_inherit");
                            if (el.checked) {
                                el.checked = false;
                                toggleValueElements(el, Element.previous(el.parentNode));
                            }
                        }
                    });
                }
            }
            var gMap = new StoreMap(latitude, longitude);
            function toggleMap()
            {
                gMapBtn.toggleClassName("delete");
                if (gMapBtn.hasClassName("delete")) {
                    gMapBtn.innerHTML = "<span><span>' . Mage::helper('adminhtml')->__('Close') . '</span></span>";
                    // Show Google Map
                    $("googleMap").show();
                    if (gMap.map == null) {
                        gMap.initGoogleMap();
                    }
                } else {
                    gMapBtn.innerHTML = "<span><span>' . $label . '</span></span>";
                    $("googleMap").hide();
                }
            }
        </script>';
        $html .= '</td></tr>';
        return $html;
    }
}

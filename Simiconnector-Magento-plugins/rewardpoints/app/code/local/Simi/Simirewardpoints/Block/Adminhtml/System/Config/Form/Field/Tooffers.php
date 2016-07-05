<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Simi_Simirewardpoints_Block_Adminhtml_System_Config_Form_Field_Tooffers extends Mage_Adminhtml_Block_System_Config_Form_Field {

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $fieldConfig = $element->getFieldConfig();
        $htmlId = $element->getHtmlId();
        $html = "<tr id='row_$htmlId'><td class='label' colspan='3'>";
        $html .= '<div style="font-weight: bold;">';
        $html .= $element->getLabel(); 
        $html .= "  <a href='".Mage::helper("adminhtml")->getUrl("*/reward_simirewardpointsreferfriends/index")."'>".$this->__('Manage Special Offers')."</a>";
        $html .= '</div></td></tr>';


        return $html;
    }

}

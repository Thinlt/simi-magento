<?php

class Simi_Simirewardpoints_Model_Simiobserver {

    public function simiSimiconnectorModelServerInitialize($observer) {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'simirewardpoints') {
            $observerObjectData['module'] = 'simirewardpoints';
        } else if ($observerObjectData['resource'] == 'simirewardpointstransactions') {
            $observerObjectData['module'] = 'simirewardpoints';
        }
        $observerObject->setData($observerObjectData);
    }

}

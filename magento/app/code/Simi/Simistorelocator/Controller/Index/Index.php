<?php

namespace Simi\Simistorelocator\Controller\Index;

class Index extends \Simi\Simistorelocator\Controller\Index {

    /**
     * Execute action.
     */
    public function execute() {
        if (!$this->systemConfig->isEnableFrontend()) {
            return $this->_getResultRedirectNoroute();
        }

        return $this->_initResultPage();
    }

}

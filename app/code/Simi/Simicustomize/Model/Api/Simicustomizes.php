<?php
namespace Simi\Simicustomize\Model\Api;

class Simicustomizes extends \Simi\Simiconnector\Model\Api\Apiabstract
{
    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            
        } else {
            
        }
    }

    public function index() {
        $result = array();
        $result['all_ids'] = array('1');
        $result['simicustomizes'] = $this->sampleGetList();
        $result['total'] = 1;
        $result['page_size'] = 15;
        $result['from'] = 0;
        return $result;
    }

    public function sampleGetList() {
        return array(
            array(
                'installed' => true
            )
        );
    }
}

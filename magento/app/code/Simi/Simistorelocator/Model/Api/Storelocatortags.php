<?php

/**
 * Created by PhpStorm.
 * User: scottsimicart
 * Date: 10/2/17
 * Time: 3:04 PM
 */

namespace Simi\Simistorelocator\Model\Api;

use Simi\Simiconnector\Model\Api\Apiabstract as Api;

class Storelocatortags extends Api
{
    public $DEFAULT_ORDER = 'tag_id';

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {

        } else {
            $tagCollection = $this->simiObjectManager
                ->create(\Simi\Simistorelocator\Model\Tag::class)
                ->getCollection();
            $this->builderQuery = $tagCollection;
        }
    }

    public function index()
    {
        $result = parent::index();

        $storeTags = $result['storelocatortags'];
        foreach ($storeTags  as $index => $item){
            $item['value'] = $item['tag_name'];
            $storeTags[$index] = $item;
        }
        $result['storelocatortags'] = $storeTags;
        return $result;
    }
}
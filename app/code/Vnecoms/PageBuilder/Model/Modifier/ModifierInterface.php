<?php
namespace Vnecoms\PageBuilder\Model\Modifier;

interface ModifierInterface 
{
    /**
     * Process the data of page builder
     * 
     * @param array $data
     * @return array
     */
    public function process($data = array());
}

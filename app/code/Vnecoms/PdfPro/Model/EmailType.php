<?php

namespace Vnecoms\PdfPro\Model;

class EmailType
{
    private $type;

    private $varCode;

    public function __construct(
        $type,
        $varCode
    ) {
        $this->type = $type;
        $this->varCode = $varCode;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getVarCode()
    {
        return $this->varCode;
    }
}

<?php

namespace Vnecoms\PdfPro\Model;

class NextEmailInfo
{
    private $templateVars;

    private $templateIdentifier;

    public function setTemplateVars($templateVars)
    {
        $this->templateVars = $templateVars;
    }

    public function getTemplateVars()
    {
        return $this->templateVars;
    }

    public function setTemplateIdentifier($templateIdentifier)
    {
        $this->templateIdentifier = $templateIdentifier;
    }

    public function getTemplateIdentifier()
    {
        return $this->templateIdentifier;
    }
}

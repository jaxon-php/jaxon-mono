<?php

use function Jaxon\jaxon;

class ClassD
{
    public function methodDa()
    {
        $xResponse = jaxon()->getResponse();
        $xResponse->html('div', 'This is the div content!!');
    }
}

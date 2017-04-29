<?php

namespace FWV\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FWVUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}

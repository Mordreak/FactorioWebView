<?php

namespace FWV\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function checkAction()
    {
        return new JsonResponse(array('isInstanceOfFWV' => true));
    }
}

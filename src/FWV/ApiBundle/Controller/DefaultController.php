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

    public function isServerRunningAction()
    {
        $manager = $this->container->get('fwv_manager.helper_manager');
        $result = array();

        try {
            $result['isServerRunning'] = $manager->isServerRunning();
        } catch (\Exception $e) {
            $result['isServerRunning'] = false;
        }

        return new JsonResponse($result);
    }
}

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
            $result['isServerRunning'] = (boolean)$manager->isServerRunning();
        } catch (\Exception $e) {
            $result['isServerRunning'] = false;
        }

        return new JsonResponse($result);
    }

    /**
     * Stops the server
     *
     * @return JsonResponse
     */
    public function stopServerAction()
    {
        $manager = $this->container->get('fwv_manager.helper_manager');
        try {
            if (!$manager->isServerRunning()) {
                return new JsonResponse(array(
                    'success' => false,
                    'error' => 'Server is not started'
                ), 412);
            }
            $manager->stopServer();
        } catch (\Exception $e) {
            return new JsonResponse(array(
                'success' => false,
                'error' => $e->getMessage()
            ), 500);
        }
        return new JsonResponse(array(
            'success' => true
        ), 200);
    }

    /**
     * Starts the server
     *
     * @return JsonResponse
     */
    public function startServerAction()
    {
        $manager = $this->container->get('fwv_manager.helper_manager');

        try {
            if ($manager->isServerRunning()) {
                return new JsonResponse(array(
                    'success' => false,
                    'answer' => 'Server already started'
                ), 412);
            }
            $manager->startServer(null, $this->get('logger'));
        } catch (\Exception $e) {
            return new JsonResponse(array(
                'success' => false,
                'answer' => $e->getMessage()
            ), 500);
        }
        return new JsonResponse(array(
            'success' => true
        ), 200);
    }

    /**
     * Gets the logs for the current last save
     *
     * @return JsonResponse
     */
    public function getLogsAction()
    {
        try {
            $parser = $this->container->get('fwv_manager.helper_parser');
            $logs = $parser->parseLog();
            return new JsonResponse(array(
                'success' => true,
                'logs' => $logs
            ), 200);
        } catch (\Exception $e) {
            return new JsonResponse(array(
                'success' => false,
                'error' => $e
            ), 500);
        }
    }
}

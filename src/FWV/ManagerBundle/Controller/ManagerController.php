<?php

namespace FWV\ManagerBundle\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ManagerController extends Controller
{
    public function indexAction()
    {
        $manager = $this->container->get('fwv_manager.helper');
        return $this->render('FWVManagerBundle:Default:index.html.twig', array(
            'files' => $manager->getSaves()
        ));
    }

    public function startServerAction()
    {
        $manager = $this->container->get('fwv_manager.helper');
        if ($manager->isServerRunning()) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => 'Le serveur est déjà démarré'
            ));
        }
        try {
            $answer = $manager->startServer('newgame');
        } catch (Exception $e) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => $e->getMessage()
            ));
        }
        return new JsonResponse(array(
            'done' => true,
            'answer' => $answer
        ));
    }

    public function stopServerAction()
    {
        $manager = $this->container->get('fwv_manager.helper');
        try {
            if (!$manager->isServerRunning()) {
                return new JsonResponse(array(
                    'done' => false,
                    'answer' => 'Le serveur n\'est pas démarré'
                ));
            }
            $answer = $manager->stopServer();
        } catch (Exception $e) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => $e->getMessage()
            ));
        }
        return new JsonResponse(array(
            'done' => true,
            'answer' => $answer
        ));
    }

    public function restartServerAction()
    {
        $manager = $this->container->get('fwv_manager.helper');
        if (!$manager->isServerRunning()) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => 'Le serveur n\'est pas démarré'
            ));
        }
        try {
            $answer = $manager->restartServer();
        } catch (Exception $e) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => $e->getMessage()
            ));
        }
        return new JsonResponse(array(
            'done' => true,
            'answer' => $answer
        ));
    }

    public function getSavesAction()
    {
        $manager = $this->container->get('fwv_manager.helper');
        return new JsonResponse(array(
            'done' => true,
            'saves' => $manager->getSaves()
        ));
    }
}

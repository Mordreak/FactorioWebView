<?php

namespace FWV\ManagerBundle\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class ManagerController extends Controller
{
    public function indexAction()
    {
        $manager = $this->container->get('fwv_manager.helper');
        return $this->render('FWVManagerBundle:Default:index.html.twig', array(
            'files' => $manager->getSaves()
        ));
    }

    public function startServerAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()) {
            return new Response('This is not ajax!', 400);
        }

        $manager = $this->container->get('fwv_manager.helper');
        if ($manager->isServerRunning()) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => 'Le serveur est déjà démarré'
            ));
        }
        try {
            $manager->startServer(null, $this->get('logger'));
        } catch (Exception $e) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => $e->getMessage()
            ));
        }
        return new JsonResponse(array(
            'done' => true
        ));
    }

    public function stopServerAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()) {
            return new Response('This is not ajax!', 400);
        }

        $manager = $this->container->get('fwv_manager.helper');
        try {
            if (!$manager->isServerRunning()) {
                return new JsonResponse(array(
                    'done' => false,
                    'answer' => 'Le serveur n\'est pas démarré'
                ));
            }
            $manager->stopServer();
        } catch (Exception $e) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => $e->getMessage()
            ));
        }
        return new JsonResponse(array(
            'done' => true
        ));
    }

    public function restartServerAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()) {
            return new Response('This is not ajax!', 400);
        }

        $manager = $this->container->get('fwv_manager.helper');
        if (!$manager->isServerRunning()) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => 'Le serveur n\'est pas démarré'
            ));
        }
        try {
            $manager->restartServer($this->get('logger'));
        } catch (Exception $e) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => $e->getMessage()
            ));
        }
        return new JsonResponse(array(
            'done' => true
        ));
    }

    public function getSavesAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()) {
            return new Response('This is not ajax!', 400);
        }

        $manager = $this->container->get('fwv_manager.helper');
        return new JsonResponse(array(
            'done' => true,
            'saves' => $manager->getSaves()
        ));
    }

    public function createGameAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()) {
            return new Response('This is not ajax!', 400);
        }

        if (!$saveName = $request->get('savename')) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => 'Veuillez fournir un nom pour la sauvegarde'
            ));
        }

        if ($saveName != preg_replace("/[^A-Za-z0-9 ]/", '', $saveName)) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => 'Seuls les caractères alphanumériques sont autorisés'
            ));
        }

        $manager = $this->container->get('fwv_manager.helper');
        try {
            $manager->createGame($saveName);
        } catch (Exception $e) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => $e->getMessage()
            ));
        }

        return new JsonResponse(array(
            'done' => true
        ));
    }
}

<?php

namespace FWV\ManagerBundle\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ManagerController extends Controller
{
    /**
     * Renders the dashboard page
     *
     * @return Response
     */
    public function dashboardAction()
    {
        $manager = $this->container->get('fwv_manager.helper_manager');
        $form = $this->createFormBuilder()
            ->add('tarball', FileType::class, array('required' => true))
            ->getForm();
        return $this->render('FWVManagerBundle:Manager:dashboard.html.twig', array(
            'files' => $manager->getSaves(),
            'form' => $form->createView()
        ));
    }

    /**
     * Starts the server
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function startServerAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()) {
            return new Response('This is not ajax!', 400);
        }

        $manager = $this->container->get('fwv_manager.helper_manager');

        try {

            if ($saveName = $request->get('savename')) {
                if ($manager->isServerRunning()) {
                    $manager->stopServer();
                }
                $manager->startServer($saveName, $this->get('logger'));
            } else {
                if ($manager->isServerRunning()) {
                    return new JsonResponse(array(
                        'done' => false,
                        'answer' => 'Server already started'
                    ));
                }
                $manager->startServer(null, $this->get('logger'));

            }
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

    /**
     * Stops the server
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function stopServerAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()) {
            return new Response('This is not ajax!', 400);
        }

        $manager = $this->container->get('fwv_manager.helper_manager');
        try {
            if (!$manager->isServerRunning()) {
                return new JsonResponse(array(
                    'done' => false,
                    'answer' => 'Server is not started'
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

    /**
     * Restarts the server
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function restartServerAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()) {
            return new Response('This is not ajax!', 400);
        }

        $manager = $this->container->get('fwv_manager.helper_manager');
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

    /**
     * retrieves informations about the save files
     *
     * @param Request $request
     * @return JsonResponse|Response
     *
     */
    public function getSavesAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()) {
            return new Response('This is not ajax!', 400);
        }

        $manager = $this->container->get('fwv_manager.helper_manager');
        return new JsonResponse(array(
            'done' => true,
            'saves' => $manager->getSaves()
        ));
    }

    /**
     * Creates a new game/save
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function createGameAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()) {
            return new Response('This is not ajax!', 400);
        }

        if (!$saveName = $request->get('savename')) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => 'Please, give a name to your save'
            ));
        }

        if ($saveName != preg_replace("/[^A-Za-z0-9 ]/", '', $saveName)) {
            return new JsonResponse(array(
                'done' => false,
                'answer' => 'Only alphanumeric characteres are allowed'
            ));
        }

        $manager = $this->container->get('fwv_manager.helper_manager');
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

    /**
     * Check wether the server is on/off
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function isServerOnAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()) {
            return new Response('This is not ajax!', 400);
        }
        $manager = $this->container->get('fwv_manager.helper_manager');
        try {
            $answer = $manager->isServerRunning() ? true : false;
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

    /**
     * Handles the form which uploads the headless server package
     * and install the server from this package.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function uploadGameAction(Request $request)
    {
        try {
            if ($request->getMethod() == 'POST') {
                $form = $this->createFormBuilder()
                    ->add('tarball', FileType::class, array('required' => true))
                    ->getForm();
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $tarballName = $form['tarball']->getData()->getClientOriginalName();

                    $form['tarball']->getData()->move('../var', $tarballName);

                    try {
                        $this->container->get('fwv_manager.helper_manager')->installGame($tarballName);
                    } catch (ProcessFailedException $e) {
                        $this->get('logger')->error($e->getMessage());
                    }
                }
                else {
                    foreach ($form->getErrors() as $error) {
                        $this->get('logger')->error($error->getMessage());
                    }
                }
            }
        } catch (Exception $e) {
            $this->get('logger')->error($e->getMessage());
        }
        return $this->redirect($this->generateUrl('fwv_manager_homepage'));
    }

    /**
     * Renders the manage page
     *
     * @return Response
     */
    public function manageAction()
    {
        $form = $this->createFormBuilder()
            ->add('zipFile', FileType::class, array('required' => true))
            ->getForm();
        return $this->render('FWVManagerBundle:Manager:manage.html.twig', array(
            'form' => $form->createView()
        ));
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
                'done' => true,
                'logs' => $logs
            ));
        } catch (Exception $e) {
            return new JsonResponse(array(
                'done' => false
            ));
        }
    }

    /**
     * Add a mod to the list of available mods
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function uploadModAction(Request $request)
    {
        try {
            if ($request->getMethod() == 'POST') {
                if (!$request->isXMLHttpRequest()) {
                    return new Response('This is not ajax!', 400);
                }
                $form = $this->createFormBuilder()
                    ->add('zipFile', FileType::class, array('required' => true))
                    ->getForm();
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $zipFile = $form['zipFile']->getData()->getClientOriginalName();

                    if (substr($zipFile, -4) != '.zip')
                        throw new \InvalidArgumentException('The uploaded file is not a .zip file');

                    $form['zipFile']->getData()->move('../var/mods', str_replace(' ', '', $zipFile));
                    return new JsonResponse(array(
                        'done' => true
                    ));
                } else {
                    foreach ($form->getErrors() as $error) {
                        $this->get('logger')->error($error->getMessage());
                        throw new Exception($error->getMessage());
                    }
                }
            }
        } catch (Exception $e) {
            return new JsonResponse(array(
                'done' => false,
                'reason' => $e->getMessage()
            ));
        }
    }

    /**
     * get all available mods
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function getModsAction(Request $request)
    {
        try {
            if (!$request->isXMLHttpRequest()) {
                return new Response('This is not ajax!', 400);
            }
            $mods = $this->container->get('fwv_manager.helper_manager')->getMods();
            return new JsonResponse(array(
                'done' => true,
                'mods' => $mods
            ));
        } catch (Exception $e) {
            return new JsonResponse(array(
                'done' => false,
                'reason' => $e->getMessage()
            ));
        }
    }

    /**
     * Renders the manage page
     *
     * @return Response
     */
    public function logsAction()
    {
        return $this->render('FWVManagerBundle:Manager:logs.html.twig');
    }

    /**
     * Activate/Disable a mod
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function toggleModAction(Request $request)
    {
        try {
            if (!$request->isXMLHttpRequest()) {
                return new Response('This is not ajax!', 400);
            }
            $modName = $request->get('modname');
            return new JsonResponse(array(
                'done' => true,
                'action' => $this->container->get('fwv_manager.helper_manager')->toggleMod($modName)
            ));
        } catch (Exception $e) {
            return new JsonResponse(array(
                'done' => false,
                'reason' => $e->getMessage()
            ));
        }
    }

    /**
     * Handles the form which uploads a custom savefile.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadSaveAction(Request $request)
    {
        try {
            if (!$request->isXMLHttpRequest()) {
                return new Response('This is not ajax!', 400);
            }
            if ($request->getMethod() == 'POST') {

                $file = $request->files->get('upload-save');

                if ($file) {
                    $saveFileName = $file->getClientOriginalName();

                    $file->move('../var', $saveFileName);

                    try {
                        $this->container->get('fwv_manager.helper_manager')->installSave($saveFileName);
                    } catch (ProcessFailedException $e) {
                        $this->get('logger')->error($e->getMessage());
                    }
                }
                else {
                    $this->get('logger')->error('File was missing from upload');
                }
            }
        } catch (Exception $e) {
            $this->get('logger')->error($e->getMessage());
            return new JsonResponse(array(
                'done' => false,
                'reason' => $e->getMessage()
            ));
        }
        return new JsonResponse(array(
            'done' => true
        ));
    }
}

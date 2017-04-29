<?php

namespace FWV\ManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ManagerController extends Controller
{
    public function indexAction()
    {
        return $this->render('FWVManagerBundle:Default:index.html.twig');
    }
}

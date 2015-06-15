<?php

namespace YamilovS\SypexGeoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('YamilovSSypexGeoBundle:Default:index.html.twig', array('name' => $name));
    }
}

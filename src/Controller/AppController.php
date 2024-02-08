<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route("/")]
    public function home()
    {
        return $this->render("home.html.twig");
    }
}

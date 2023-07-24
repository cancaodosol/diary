<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ElfinderController extends AbstractController
{
    /**
     * @Route("/open_elfinder", name="app_elfinder")
     */
    public function index(Request $request): Response
    {
        return $this->redirect('./elfinder');
    }
}
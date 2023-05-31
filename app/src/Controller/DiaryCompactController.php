<?php

namespace App\Controller;

use App\Entity\DiaryCompact;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class DiaryCompactController extends AbstractController
{
    /**
     * @Route("/diaryc", name="app_diary_compact")
     */
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $diaries = $doctrine->getRepository(DiaryCompact::class)
            ->findBy([], ["date" => "DESC"]);

        return $this->render('diary/views.html.twig', [
            'form_name' => '',
            'diaries' => $diaries,
        ]);
    }
}

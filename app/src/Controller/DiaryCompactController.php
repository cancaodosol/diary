<?php

namespace App\Controller;

use App\Entity\DiaryCompact;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\NoteTags;

class DiaryCompactController extends AbstractController
{
    /**
     * @Route("/diaryc", name="app_diary_compact")
     */
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $diaries = $doctrine->getRepository(DiaryCompact::class)
            ->findBy([], ["date" => "DESC"]);

        $tags = $doctrine->getRepository(NoteTags::class)->findAll();

        return $this->render('diary/views.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'diaries' => $diaries,
        ]);
    }

    /**
     * @Route("/diaryc_r", name="app_diary_compact_r")
     */
    public function index_r(Request $request, ManagerRegistry $doctrine): Response
    {
        $diaries = $doctrine->getRepository(DiaryCompact::class)
            ->findBy([], ["date" => "ASC"]);

        $tags = $doctrine->getRepository(NoteTags::class)->findAll();

        return $this->render('diary/views.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'diaries' => $diaries,
        ]);
    }
}

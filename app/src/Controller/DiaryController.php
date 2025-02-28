<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Diary;
use App\Entity\DiaryCompact;
use App\Entity\NoteTags;
use App\Form\DiaryType;

class DiaryController extends AbstractController
{
    /**
     * @Route("/diary", name="app_diary")
     */
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        return $this->redirectToRoute('app_unitary', []);

        $diaries = $doctrine->getRepository(Diary::class)
            ->findBy([], ["date" => "DESC"]);

        $tags = $doctrine->getRepository(NoteTags::class)->findAll();

        return $this->render('diary/views.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'diaries' => $diaries,
        ]);
    }

    /**
     * @Route("/diary/new", name="new_diary")
     */
    public function newDiary(Request $request, ManagerRegistry $doctrine): Response
    {
        $diary = new Diary();

        $form = $this->createForm(DiaryType::class, $diary);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $diary = $form->getData();

            // 更新処理
            $entityManager = $doctrine->getManager();
            $entityManager->persist($diary);
            $entityManager->flush();

            return $this->redirectToRoute('new_unitary_with_compact', [
                'date' => $diary->getDateString()
            ]);
        }

        $tags = $doctrine->getRepository(NoteTags::class)->findAll();

        return $this->renderForm('./new.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/diary/edit/{date}", name="edit_diary")
     */
    public function editDiary(Request $request, ManagerRegistry $doctrine, string $date): Response
    {
        $date = $this->transferDate($date);
        $diary = $doctrine->getRepository(Diary::class)->findOneBy(['date' => DateTime::createFromFormat('Y-m-d', $date)]);
        if(!$diary)
        {
            $diary = new Diary();
            $diary->setDate(DateTime::createFromFormat('Y-m-d', $date));
        }

        $form = $this->createForm(DiaryType::class, $diary);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $diary = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($diary);
            $entityManager->flush();
            return $this->redirectToRoute('new_unitary_with_compact', [
                'date' => $diary->getDateString()
            ]);
        }

        $tags = $doctrine->getRepository(NoteTags::class)->findAll();

        return $this->renderForm('./new.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/diary/view/{date}", name="view_diary")
     */
    public function viewDiary(Request $request, ManagerRegistry $doctrine, string $date): Response
    {
        $date = $this->transferDate($date);
        $diary = $doctrine->getRepository(Diary::class)->findOneBy(['date' => DateTime::createFromFormat('Y-m-d', $date)]);;
        if(!$diary)
        {
            $diary = new Diary();
        }

        $form = $this->createForm(DiaryType::class, $diary);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $diary = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($diary);
            $entityManager->flush();
            return $this->redirectToRoute('view_diary', [
                'date' => $date
            ]);
        }

        $tags = $doctrine->getRepository(NoteTags::class)->findAll();

        return $this->renderForm('./diary/view.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'diary' => $diary,
        ]);
    }

    /** 
    特定の日付文字列が来た場合は、変換して返す。today, yesterday
    **/
    private function transferDate(string $date): string
    {
        switch ($date) {
            case 'today':
                return date('Y-m-d');
                break;
            case 'yesterday':
                return date('Y-m-d', strtotime("-1 days"));
                break;
            default:
                return $date;
                break;
            }
    }
}
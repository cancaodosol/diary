<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Diary;

use App\Form\DiaryType;

class DiaryController extends AbstractController
{
    /**
     * @Route("/diary", name="app_diary")
     */
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {

        $diaries = $doctrine->getRepository(Diary::class)
            ->findBy([], ["date" => "DESC"]);

        return $this->render('diary/views.html.twig', [
            'form_name' => '',
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
            $startedWritingAt = $diary->getStartedWritingAt();
            $dDiary = $doctrine->getRepository(Diary::class)->findOneBy(['date' => DateTime::createFromFormat('Y-m-d', $diary->getDateString())]);
            if($dDiary)
            {
                $text = $dDiary->getText().
                    "<hr><strong>".$startedWritingAt." 記入開始"."</strong><br>".
                    $diary->getText().
                    "<br>".date('↑ n/j H:i 記入終了')."<hr>";
                $dDiary->setText($text);
                $diary = $dDiary;
            }
            $entityManager = $doctrine->getManager();
            $entityManager->persist($diary);
            $entityManager->flush();
            return $this->redirectToRoute('view_diary', [
                'date' => $diary->getDateString()
            ]);
        }

        return $this->renderForm('./new.html.twig', [
            'form_name' => '',
            'form' => $form,
        ]);
    }

    /**
     * @Route("/diary/edit/{date}", name="edit_diary")
     */
    public function editDiary(Request $request, ManagerRegistry $doctrine, string $date): Response
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
                'date' => $diary->getDateString()
            ]);
        }

        return $this->renderForm('./new.html.twig', [
            'form_name' => '',
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

        return $this->renderForm('./diary/view.html.twig', [
            'form_name' => '',
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
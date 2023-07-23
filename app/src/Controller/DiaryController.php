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
     * @Route("/diary_r", name="app_diary_r")
     */
    public function index_r(Request $request, ManagerRegistry $doctrine): Response
    {
        $diaries = $doctrine->getRepository(Diary::class)
            ->findBy([], ["date" => "ASC"]);

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

            // 同日の行動記録の末尾に、追記する。
            $title = "<strong>".$diary->getStartedAndFinishedAt()."　".$diary->getTitle()."</strong>";
            $dDiary = $doctrine->getRepository(Diary::class)->findOneBy(['date' => DateTime::createFromFormat('Y-m-d', $diary->getDateString())]);
            if($dDiary)
            {
                $text = $dDiary->getText().
                    "\r\n\r\n<hr>\r\n".$this->_createTitleText($title, $diary->getText());
                $dDiary->setText($text);
                $diary = $dDiary;
            }
            else
            {
                $text = $this->_createTitleText($title, $diary->getText());
                $diary->setText($text);
            }

            // 更新処理
            $entityManager = $doctrine->getManager();
            $entityManager->persist($diary);
            $entityManager->flush();
            $this->updateDiaryCompact($doctrine, $diary);

            return $this->redirectToRoute('app_diary_compact');
        }

        $tags = $doctrine->getRepository(NoteTags::class)->findAll();

        return $this->renderForm('./new.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'form' => $form,
        ]);
    }

    private function _createTitleText(string $title, $text): string
    {
        $result = '';
        if(trim($text))
        {
            $result = $title."\r\n\r\n".$text."\r\n";
        }
        else
        {
            $result = $title."\r\n";
        }
        return $result;
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
        }
        $diary->setTitle(".");
        $diary->setStartedAt(".");
        $diary->setFinishedAt(".");

        $form = $this->createForm(DiaryType::class, $diary);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $diary = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($diary);
            $entityManager->flush();
            $this->updateDiaryCompact($doctrine, $diary);
            return $this->redirectToRoute('view_diary', [
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

    private function updateDiaryCompact(ManagerRegistry $doctrine, Diary $diary)
    {
        $diaryC = $doctrine->getRepository(DiaryCompact::class)->findOneBy(['date' => DateTime::createFromFormat('Y-m-d', $diary->getDateString())]);
        if(!$diaryC)
        {
            $diaryC = new DiaryCompact();
        }

        // strongタグの文字列を抽出する
        // 参考：https://qiita.com/kanaxx/items/daca1c57e48e0a8d674a
        $strongs = "";
        $pattern = '@<strong>(.*)</strong>@';
        if( preg_match_all($pattern, $diary->getText(), $result) ){
            var_dump($result);
            foreach($result[1] as $strong)
            {
                $strongs = $strongs."<br>".$strong;
            }
        }else{
            $strongs = 'No match' . PHP_EOL;
        }
        $diaryC->setDiaryId($diary->getId());
        $diaryC->setDate($diary->getDate());
        $diaryC->setText($strongs);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($diaryC);
        $entityManager->flush();
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
<?php

namespace App\Controller;

use App\Entity\NoteTags;
use App\Entity\UnitaryNote;
use App\Entity\Diary;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\UnitaryNoteType;

class UnitaryNoteController extends AbstractController
{
    /**
     * @Route("/unitary_v/{tagName}", name="app_unitary")
     */
    public function index(ManagerRegistry $doctrine, string $tagName=''): Response
    {
        if($tagName)
        {
            $tag = $doctrine->getRepository(NoteTags::class)
                ->findOneBy(["name" => $tagName]);
            $notesInterator = $tag->getUnitaryNotes()->getIterator();
            $notesInterator->uasort(function($pA , $pB){
                    if($pA->getDate() < $pB->getDate()) return 1;
                    if($pA->getDate() > $pB->getDate()) return -1;
                    if($pA->getTitle() < $pB->getTitle()) return 1;
                    if($pA->getTitle() > $pB->getTitle()) return -1;
                    return 0;
                });
            $notes = iterator_to_array($notesInterator, false);
        }
        else
        {
            $notes = $doctrine->getRepository(UnitaryNote::class)
                ->findBy([], ["date" => "DESC"]);
        }

        $tags = $doctrine->getRepository(NoteTags::class)->findAll();

        return $this->render('unitary_note/views.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'notes' => $notes,
        ]);
    }

    /**
     * @Route("/unitary_v/d/{date}", name="app_unitary_date")
     */
    public function index_diary(ManagerRegistry $doctrine, string $date): Response
    {
        $date = $this->transferDate($date);
        $notes = $doctrine->getRepository(UnitaryNote::class)->findBy(
            ['date' => DateTime::createFromFormat('Y-m-d', $date)],
            ["title" => "ASC"]);
        
        if(count($notes) == 0)
        {
            $notes = [];
            $note = new UnitaryNote();
            $note->setDate(DateTime::createFromFormat('Y-m-d', $date));
            $notes[] = $note;
        }

        $tags = $doctrine->getRepository(NoteTags::class)->findAll();

        return $this->render('unitary_note/views.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'notes' => $notes,
        ]);
    }

    /**
     * @Route("/unitary_r/{div}", name="app_unitary_r")
     */
    public function index_r(ManagerRegistry $doctrine, string $div): Response
    {
        $notes = $doctrine->getRepository(UnitaryNote::class)
            ->findBy(["div" => $div], ["date" => "ASC"]);

        $tags = $doctrine->getRepository(NoteTags::class)->findAll();

        return $this->render('unitary_note/views.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'notes' => $notes,
        ]);
    }

    /**
     * @Route("/unitary/new", name="new_unitary")
     */
    public function newUnitary(Request $request, ManagerRegistry $doctrine): Response
    {
        $note = new UnitaryNote();

        $form = $this->createForm(UnitaryNoteType::class, $note);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $note = $form->getData();
            $title = $note->getStartedAndFinishedAt()."　".$note->getTitle();
            $note->setTitle($title);

            // 更新処理
            $entityManager = $doctrine->getManager();
            $entityManager->persist($note);
            $entityManager->flush();

            return $this->redirectToRoute('app_unitary_date', [
                'date' => $note->getDateString()
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
     * @Route("/unitary/edit/{id}", name="edit_unitary")
     */
    public function editUnitary(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $note = $doctrine->getRepository(UnitaryNote::class)->findOneBy(['id' => $id]);
        if(!$note)
        {
            $note = new UnitaryNote('');
        }
        $note->setStartedAt(".");
        $note->setFinishedAt(".");

        $form = $this->createForm(UnitaryNoteType::class, $note);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $note = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($note);
            $entityManager->flush();
            return $this->redirectToRoute('app_unitary_date', [
                'date' => $note->getDateString()
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
     * @Route("/unitary/view/{id}", name="view_unitary")
     */
    public function viewUnitary(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $note = $doctrine->getRepository(UnitaryNote::class)->findOneBy(['id' => $id]);
        if(!$note)
        {
            $note = new UnitaryNote('');
        }

        $tags = $doctrine->getRepository(NoteTags::class)->findAll();

        return $this->renderForm('unitary_note/view.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'note' => $note,
        ]);
    }

    /**
     * @Route("/unitary/transfer", name="transfer_unitary")
     */
    public function transferUnitary(Request $request, ManagerRegistry $doctrine): Response
    {
        $diaries = $doctrine->getRepository(Diary::class)
            ->findBy([], ["date" => "DESC"]);
        
        foreach($diaries as $diary){
            $notes = [];
            $units = explode('<hr>', $diary->getText());
            foreach($units as $unit)
            {
                $pattern = '@<strong>(.*)</strong>@';
                $title = '';
                $text = '';
                if( preg_match_all($pattern, $unit, $result) ){
                    $title_html = $result[0][0];
                    $title = $result[1][0];
                    $nowHour = (int)substr($title, 0, 2);
                    $text = str_replace($title_html, '', $unit);
                }
                $text = trim($text);
                $note = New UnitaryNote();
                $note->setDate($diary->getDate());
                $note->setTitle($title);
                $note->setText($text);
                $notes[] = $note;
            }

            $preHour = 0;
            $plus24hours = false;
            foreach($notes as $note){
                $title = $note->getTitle();

                // すでに同じタイトルのものがあった場合は、
                $dbNote = $doctrine->getRepository(UnitaryNote::class)->findOneBy(['title' => $title, 'date' => $note->getDate()]);
                if($dbNote) continue;

                $nowHour = (int)substr($title, 0, 2);
                if($plus24hours == false && $nowHour < $preHour) $plus24hours = true;
                if($plus24hours == true){
                    $title = str_replace(substr($title, 0, 3), sprintf("%'.02d:", $nowHour + 24), $title);
                }
                if($title != $note->getTitle()) $note->setTitle($title);
                $entityManager = $doctrine->getManager();
                $entityManager->persist($note);
                $entityManager->flush();
                $preHour = $nowHour;
            }
        }

        return $this->redirectToRoute('app_unitary');
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

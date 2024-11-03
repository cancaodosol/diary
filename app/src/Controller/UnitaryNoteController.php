<?php

namespace App\Controller;

use App\Entity\NoteTags;
use App\Entity\UnitaryNote;
use App\Entity\Diary;
use App\Helpers\DateHelper;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\UnitaryNoteType;

class UnitaryNoteController extends AbstractController
{
    /**
     * @Route("/unitary/t/{tagName}/{mode}", name="app_unitary")
     */
    public function index(ManagerRegistry $doctrine, string $tagName='', string $mode=''): Response
    {
        $tags = $doctrine->getRepository(NoteTags::class)->findBy([], ["name" => "ASC"]);

        if($tagName == '')
        {
            return $this->redirectToRoute('app_unitary',['tagName' => 'All', 'mode' => $mode]);
        }

        if($tagName == 'All')
        {
            $notes = $doctrine->getRepository(UnitaryNote::class)
                ->findRecently((new \DateTime('now'))->modify('-3 months'));

            if($mode == "calender")
            {
                $dateHelper = new DateHelper();
                $calender_dates = $dateHelper->getDatesInTheLastWeeks(new DateTime(), 16);
                return $this->viewNotesByCalenderFormat($tags, null, $notes, $calender_dates);
            }

            return $this->render('unitary_note/views_units.html.twig', [
                'form_name' => '',
                'tags' => $tags,
                'note_units' => $this->createNoteUnits($notes),
            ]);
        }

        if($tagName == 'Full')
        {
            $notes = $doctrine->getRepository(UnitaryNote::class)
            ->findBy([], ["date" => "DESC", "title" => "ASC"]);

            if($mode == "calender")
            {
                $dateHelper = new DateHelper();
                $calender_dates = $dateHelper->getDatesInTheLastWeeks(new DateTime(), 160);
                return $this->viewNotesByCalenderFormat($tags, null, $notes, $calender_dates);
            }

            return $this->render('unitary_note/views_units.html.twig', [
                'form_name' => '',
                'tags' => $tags,
                'note_units' => $this->createNoteUnits($notes),
            ]);
        }

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

        if($mode == "card")
        {
            return $this->render('unitary_note/views_cards.html.twig', [
                'form_name' => '',
                'tags' => $tags,
                'thisTag' => $tag,
                'notes' => $notes,
            ]);
        } elseif($mode == "calender") {
            $dateHelper = new DateHelper();
            $calender_dates = $dateHelper->getDatesInTheLastWeeks(new DateTime(), 16);
            return $this->viewNotesByCalenderFormat($tags, $tag, $notes, $calender_dates);
        } else {
            return $this->render('unitary_note/views.html.twig', [
                'form_name' => '',
                'tags' => $tags,
                'thisTag' => $tag,
                'notes' => $notes,
            ]);
        }
    }

    private function viewNotesByCalenderFormat($tags, $tag, $notes, $calender_dates): Response
    {
        $calender_notes = [];
        $note_index = 0;
        for ($i = 0; $i < count($calender_dates); $i++) {
            $calender_date = $calender_dates[$i];
            $calender_date_notes = [];
            for ($j = $note_index; $j < count($notes); $j++) {
                $note = $notes[$j];
                if($calender_date->format('Y-n-j') != $note->getDate()->format('Y-n-j')){
                    if(count($calender_date_notes) == 0) continue;
                    $note_index = $j;
                    break;
                }
                $calender_date_notes[] = $note;
            }
            $calender_notes[] = [
                "date" => $calender_date,
                "notes" => $calender_date_notes,
            ];
        }

        $calender_notes = array_reverse($calender_notes);

        return $this->render('unitary_note/views_calender.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'thisTag' => $tag,
            'notes' => $notes,
            'calender_notes' => $calender_notes,
        ]);
    }

    /**
     * @Route("/unitary/d/{date}", name="app_unitary_date")
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

        $tags = $doctrine->getRepository(NoteTags::class)->findBy([], ["name" => "ASC"]);

        return $this->render('unitary_note/views_units.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'note_units' => $this->createNoteUnits($notes),
        ]);
    }

    /**
     * @Route("/unitary/compact", name="app_unitary_compact")
     */
    public function index_compact(ManagerRegistry $doctrine): Response
    {
        $notes = $doctrine->getRepository(UnitaryNote::class)
            ->findBy([], ["date" => "DESC", "title" => "ASC"]);

        $tags = $doctrine->getRepository(NoteTags::class)->findBy([], ["name" => "ASC"]);

        return $this->render('unitary_note/views_compact.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'note_units' => $this->createNoteUnits($notes),
        ]);
    }

    /**
     * @Route("/unitary_r/{div}", name="app_unitary_r")
     */
    public function index_r(ManagerRegistry $doctrine, string $div): Response
    {
        $notes = $doctrine->getRepository(UnitaryNote::class)
            ->findBy(["div" => $div], ["date" => "ASC"]);

        $tags = $doctrine->getRepository(NoteTags::class)->findBy([], ["name" => "ASC"]);

        return $this->render('unitary_note/views.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'notes' => $notes,
        ]);
    }

    /**
     * @Route("/unitary/new/{date}", name="new_unitary")
     */
    public function newUnitary(Request $request, ManagerRegistry $doctrine, string $date=''): Response
    {
        $note = new UnitaryNote();
        if($date != '')
        {
            $note->setDate(DateTime::createFromFormat('Y-m-d', $date));
        }

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

            return $this->redirectToRoute('new_unitary_with_compact', [
                'date' => $note->getDateString()
            ]);
        }

        $tags = $doctrine->getRepository(NoteTags::class)->findBy([], ["name" => "ASC"]);

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

        $tags = $doctrine->getRepository(NoteTags::class)->findBy([], ["name" => "ASC"]);

        return $this->renderForm('./new.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/api/unitary/3days/{date}", name="api_get_unitary_3days")
     */
    public function apiGetUnitary3days(Request $request, ManagerRegistry $doctrine, string $date=''): JsonResponse
    {
        $note = new UnitaryNote();
        if($date == '') $date = "today";
        $date = $this->transferDate($date);

        $notes = $doctrine->getRepository(UnitaryNote::class)
            ->findInTerm(
                (DateTime::createFromFormat('Y-m-d', $date))->setTime(0, 0)->modify('-2 days'),
                (DateTime::createFromFormat('Y-m-d', $date))->setTime(0, 0)->modify('+1 days')
            );

        $units = $this->createNoteUnits($notes);
        
        $results = [];
        foreach ($units as $unit) {
            $newUnit = [
                'date' => $unit['date'],
                'dateWithYoubi' => $unit['dateWithYoubi'],
                'preDate' => $unit['preDate'],
                'nextDate' => $unit['nextDate'],
                'notes' => []
            ];
            foreach ($unit["notes"] as $note) {
                $newUnit["notes"][] = $note->toArray();
            }
            $results[] = $newUnit;
        }

        return new JsonResponse(["units" => $results]);
    }

    /**
     * @Route("/unitary/new_w/{date}", name="new_unitary_with_compact")
     */
    public function newUnitaryWithCompact(Request $request, ManagerRegistry $doctrine, string $date=''): Response
    {
        $note = new UnitaryNote();
        if($date == '') $date = "today";
        $date = $this->transferDate($date);
        if($date != '')
        {
            $note->setDate(DateTime::createFromFormat('Y-m-d', $date));
        }

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

            return $this->redirectToRoute('new_unitary_with_compact', [
                'date' => $note->getDateString()
            ]);
        }

        $notes = $doctrine->getRepository(UnitaryNote::class)
            ->findInTerm(
                (DateTime::createFromFormat('Y-m-d', $date))->setTime(0, 0)->modify('-2 days'),
                (DateTime::createFromFormat('Y-m-d', $date))->setTime(0, 0)->modify('+1 days')
            );

        $tags = $doctrine->getRepository(NoteTags::class)->findBy([], ["name" => "ASC"]);

        return $this->renderForm('./diary_compact/withnew.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'form' => $form,
            'note_units' => $this->createNoteUnits($notes),
        ]);
    }

    /**
     * @Route("/unitary/edit_w/{id}", name="edit_unitary_with_compact")
     */
    public function editUnitaryWithCompact(Request $request, ManagerRegistry $doctrine, int $id): Response
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
            return $this->redirectToRoute('new_unitary_with_compact', [
                'date' => $note->getDateString()
            ]);
        }

        $notes = $doctrine->getRepository(UnitaryNote::class)
            ->findInTerm(
                (clone $note->getDate())->setTime(0, 0)->modify('-2 days'),
                (clone $note->getDate())->setTime(0, 0)->modify('+1 days')
            );

        $tags = $doctrine->getRepository(NoteTags::class)->findBy([], ["name" => "ASC"]);

        return $this->renderForm('./diary_compact/withnew.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'form' => $form,
            'note_units' => $this->createNoteUnits($notes),
        ]);

        $tags = $doctrine->getRepository(NoteTags::class)->findBy([], ["name" => "ASC"]);

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

        $tags = $doctrine->getRepository(NoteTags::class)->findBy([], ["name" => "ASC"]);

        return $this->renderForm('unitary_note/view.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'note' => $note,
        ]);
    }

    /**
     * @Route("/unitary/view_raw/{id}", name="view_raw_unitary")
     */
    public function viewRawUnitary(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $note = $doctrine->getRepository(UnitaryNote::class)->findOneBy(['id' => $id]);
        if(!$note)
        {
            $note = new UnitaryNote('');
        }

        $tags = $doctrine->getRepository(NoteTags::class)->findBy([], ["name" => "ASC"]);

        return $this->renderForm('unitary_note/view_raw.html.twig', [
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
     * 特定の日付文字列が来た場合は、変換して返す。today, yesterday
     */
    private function transferDate(string $date): string
    {
        switch ($date) {
            case 'today':
                return date('Y-m-d');
                break;
            case 'yesterday':
                return date('Y-m-d', strtotime("-1 days"));
                break;
            case '-1week':
                return date('Y-m-d', strtotime("-7 days"));
                break;
            case '-10days':
                return date('Y-m-d', strtotime("-10 days"));
                break;
            case '-2weeks':
                return date('Y-m-d', strtotime("-14 days"));
                break;
            case '-1month':
                return date('Y-m-d', strtotime("-1 months"));
                break;
            case '-2months':
                return date('Y-m-d', strtotime("-2 months"));
                break;
            case '-3months':
                return date('Y-m-d', strtotime("-3 months"));
                break;
            case '-100days':
                return date('Y-m-d', strtotime("-100 days"));
                break;
            default:
                return $date;
                break;
            }
    }

    private function createNoteUnits($notes)
    {
        $noteUnits = [];
        if(count($notes) == 0) return $noteUnits;
        $preDate = $notes[0]->getDateString();
        $unit = [
            'date' => $preDate,
            'dateWithYoubi' => $notes[0]->getDateStringWithYoubi(),
            'preDate' => $notes[0]->getPreDateString(),
            'nextDate' => $notes[0]->getNextDateString(),
            'notes' => []
        ];
        foreach($notes as $note)
        {
            if($preDate == $note->getDateString())
            {
                $unit['notes'][] = $note;
            }
            else
            {
                $noteUnits[] = $unit;

                $nowDate = $note->getDateString();
                $newUnit = [];
                $newUnit = [
                    'date' => $nowDate,
                    'dateWithYoubi' => $note->getDateStringWithYoubi(),
                    'preDate' => $note->getPreDateString(),
                    'nextDate' => $note->getNextDateString(),
                    'notes' => []
                ];

                $unit = $newUnit;
                $preDate = $nowDate;
                $unit['notes'][] = $note;
            }
        }
        $noteUnits[] = $unit;
        return $noteUnits;
    }
}

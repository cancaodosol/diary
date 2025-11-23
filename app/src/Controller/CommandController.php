<?php

namespace App\Controller;

use App\Entity\DiaryCompact;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\UnitaryNote;

class CommandController extends BaseController
{
    /**
     * @Route("/command", name="app_command")
     */
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $keyword = $request->request->get("keyword");
        $date = $request->request->get("date");

        if (str_starts_with($keyword, ':')) {
           return $this->executeCommand($keyword, $doctrine);
        }

        return $this->executeSearchNotes($keyword, $date, $doctrine);
    }

    private function executeCommand(string $command, ManagerRegistry $doctrine): Response
    {
        $commandKeys = explode(' ', trim($command));

        if($commandKeys[0] === ':w' || $commandKeys[0] === 'write'){
            $ddate = date('Y-m-d', strtotime(($commandKeys[1] ?? false) ? $commandKeys[1].' day' : 'now'));
            return $this->redirectToRoute('new_unitary_with_compact', ['date' => $ddate]);
        }

        if($commandKeys[0] === ':v' || $commandKeys[0] === 'view'){
            $ddate = date('Y-m-d', strtotime(($commandKeys[1] ?? false) ? $commandKeys[1].' day' : 'now'));
            return $this->redirectToRoute('app_unitary_date', ['date' => $ddate]);
        }

        if($commandKeys[0] === ':e' || $commandKeys[0] === 'edit'){
            $num = isset($commandKeys[1]) && is_numeric($commandKeys[1]) ? (int)$commandKeys[1] : 1;
            $note = $doctrine->getRepository(UnitaryNote::class)->findNthLatest($num);
            return $this->redirectToRoute('edit_unitary_with_compact', ['id' => $note->getId()]);
        }

        return $this->executeSearchNotes($command, '', $doctrine);
    }


    private function executeSearchNotes(string $keyword, string $date, ManagerRegistry $doctrine): Response
    {
        if($keyword) {
            $tags = $this->getTags($doctrine);
            $notes = $doctrine->getRepository(UnitaryNote::class)->findByKeyword($keyword);
            return $this->render('unitary_note/views.html.twig', [
                'form_name' => '',
                'tags' => $tags,
                'thisTag' => '',
                'notes' => $notes,
                'searchKeyword' => $keyword,
            ]);
        }

        return $this->redirectToRoute('app_unitary', [
            'date' => $date,
            'mode' => 'calender'
        ]);
    }
}

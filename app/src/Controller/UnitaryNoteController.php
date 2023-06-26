<?php

namespace App\Controller;

use App\Entity\NoteTags;
use App\Entity\UnitaryNote;
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

            // 更新処理
            $entityManager = $doctrine->getManager();
            $entityManager->persist($note);
            $entityManager->flush();

            // 更新後は、同じタグ情報で新規登録画面へ。
            $note->clearItem();
            $form = $this->createForm(UnitaryNoteType::class, $note);
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

        $form = $this->createForm(UnitaryNoteType::class, $note);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $note = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($note);
            $entityManager->flush();
            return $this->redirectToRoute('view_unitary', [
                'id' => $note->getId()
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
}

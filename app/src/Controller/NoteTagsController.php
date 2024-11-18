<?php

namespace App\Controller;

use App\Entity\NoteTags;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\NoteTagsType;

class NoteTagsController extends BaseController
{
    /**
     * @Route("/note/tags", name="app_note_tags")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $tags = $this->getTags($doctrine);
        return $this->render('note_tags/index.html.twig', [
            'controller_name' => 'NoteTagsController',
            'tags' => $tags,
        ]);
    }

    /**
     * @Route("/note/tags/new", name="new_note_tags")
     */
    public function newNoteTags(Request $request, ManagerRegistry $doctrine): Response
    {
        $tag = new NoteTags();

        $form = $this->createForm(NoteTagsType::class, $tag);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $tag = $form->getData();

            // 更新処理
            $entityManager = $doctrine->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('app_note_tags');
        }

        $tags = $this->getTags($doctrine);

        return $this->renderForm('./new.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/note/tags/edit/{id}", name="edit_note_tags")
     */
    public function editNoteTags(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $tag = $doctrine->getRepository(NoteTags::class)->find($id);
        $form = $this->createForm(NoteTagsType::class, $tag);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $tag = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();
            return $this->redirectToRoute('app_note_tags');
        }

        $tags = $this->getTags($doctrine);

        return $this->renderForm('./new.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'form' => $form,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\JournalCategory;
use App\Form\JournalCategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class JournalCategoryController extends BaseController
{
    /**
     * @Route("/journal/categories", name="app_journal_categories")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $tags = $this->getTags($doctrine);
        $categories = $doctrine->getRepository(JournalCategory::class)->findBy([], ['name' => 'ASC']);

        return $this->render('journal_category/index.html.twig', [
            'tags' => $tags,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/journal/categories/new", name="new_journal_category")
     */
    public function newJournalCategory(Request $request, ManagerRegistry $doctrine): Response
    {
        $category = new JournalCategory();

        $form = $this->createForm(JournalCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_journal_categories');
        }

        $tags = $this->getTags($doctrine);

        return $this->renderForm('./new.html.twig', [
            'form_name' => '仕訳分類の追加',
            'tags' => $tags,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/journal/categories/edit/{id}", name="edit_journal_category")
     */
    public function editJournalCategory(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $category = $doctrine->getRepository(JournalCategory::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException('指定された仕訳分類が見つかりません。');
        }

        $form = $this->createForm(JournalCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();

            return $this->redirectToRoute('app_journal_categories');
        }

        $tags = $this->getTags($doctrine);

        return $this->renderForm('./new.html.twig', [
            'form_name' => '仕訳分類の編集',
            'tags' => $tags,
            'form' => $form,
        ]);
    }
}

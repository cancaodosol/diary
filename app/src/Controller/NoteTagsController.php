<?php

namespace App\Controller;

use App\Entity\NoteTags;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $editTags = $doctrine->getRepository(NoteTags::class)->findBy(
            [],
            ['parentTagId' => 'ASC','sortOrder' => 'ASC', 'id' => 'ASC']
        );
        return $this->render('note_tags/index.html.twig', [
            'tags' => $tags,
            'editTags' => $editTags,
        ]);
    }

    /**
     * @Route("/note/tags/add", name="add_note_tag", methods={"POST"})
     */
    public function addTag(Request $request, ManagerRegistry $doctrine): Response
    {
        if (!$this->isCsrfTokenValid('add_note_tag', $request->request->get('_token'))) {
            $this->addFlash('error', 'CSRFトークンが無効です。');
            return $this->redirectToRoute('app_note_tags');
        }

        $name = trim($request->request->get('name', ''));
        if ($name === '') {
            $this->addFlash('error', 'タグ名は必須です。');
            return $this->redirectToRoute('app_note_tags');
        }

        $tag = new NoteTags();
        $tag->setName($name);
        $tag->setDisplayColor($this->resolveDisplayColor($request->request->get('displayColor', '')));
        $tag->setDisplayType($this->resolveDisplayType($request->request->get('displayType', '')));
        $tag->setDescription($request->request->get('description') ?: null);
        $tag->setSortOrder((int) $request->request->get('sortOrder', 0));

        $parentTagId = $this->resolveParentTagId(
            $request->request->get('parentTagId', ''),
            null,
            $doctrine
        );
        if ($parentTagId === false) {
            $this->addFlash('error', '指定した親IDは存在しません。');
            return $this->redirectToRoute('app_note_tags');
        }
        $tag->setParentTagId($parentTagId);

        $em = $doctrine->getManager();
        $em->persist($tag);
        $em->flush();

        $this->addFlash('success', 'タグを追加しました。');
        return $this->redirectToRoute('app_note_tags');
    }

    /**
     * @Route("/note/tags/{id}/update", name="update_note_tag", methods={"POST"})
     */
    public function updateTag(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        if (!$this->isCsrfTokenValid('update_note_tag_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'CSRFトークンが無効です。');
            return $this->redirectToRoute('app_note_tags');
        }

        $tag = $doctrine->getRepository(NoteTags::class)->find($id);
        if (!$tag) {
            $this->addFlash('error', 'タグが見つかりません。');
            return $this->redirectToRoute('app_note_tags');
        }

        $name = trim($request->request->get('name', ''));
        if ($name === '') {
            $this->addFlash('error', 'タグ名は必須です。');
            return $this->redirectToRoute('app_note_tags');
        }

        $parentTagId = $this->resolveParentTagId(
            $request->request->get('parentTagId', ''),
            $id,
            $doctrine
        );
        if ($parentTagId === false) {
            $this->addFlash('error', '指定した親IDは無効です（自分自身または存在しないIDです）。');
            return $this->redirectToRoute('app_note_tags');
        }

        $tag->setName($name);
        $tag->setDisplayColor($this->resolveDisplayColor($request->request->get('displayColor', '')));
        $tag->setDisplayType($this->resolveDisplayType($request->request->get('displayType', '')));
        $tag->setSortOrder((int) $request->request->get('sortOrder', 0));
        $tag->setParentTagId($parentTagId);

        $doctrine->getManager()->flush();

        $this->addFlash('success', 'タグを更新しました。');
        return $this->redirectToRoute('app_note_tags');
    }

    /**
     * @Route("/note/tags/{id}/delete", name="delete_note_tag", methods={"POST"})
     */
    public function deleteTag(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        if (!$this->isCsrfTokenValid('delete_note_tag_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'CSRFトークンが無効です。');
            return $this->redirectToRoute('app_note_tags');
        }

        $tag = $doctrine->getRepository(NoteTags::class)->find($id);
        if (!$tag) {
            $this->addFlash('error', 'タグが見つかりません。');
            return $this->redirectToRoute('app_note_tags');
        }

        $em = $doctrine->getManager();

        // 子タグの親IDをnullにしてから削除する
        $children = $doctrine->getRepository(NoteTags::class)->findBy(['parentTagId' => $id]);
        foreach ($children as $child) {
            $child->setParentTagId(null);
        }
        $em->flush();

        $em->remove($tag);
        $em->flush();

        $this->addFlash('success', 'タグを削除しました。');
        return $this->redirectToRoute('app_note_tags');
    }

    /**
     * @Route("/note/tags/new", name="new_note_tags")
     */
    public function newNoteTags(Request $request, ManagerRegistry $doctrine): Response
    {
        $tag = new NoteTags();

        $form = $this->createForm(NoteTagsType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tag = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('app_note_tags');
        }

        $tags = $this->getTags($doctrine);

        return $this->renderForm('./new.html.twig', [
            'form_name' => 'タグ追加',
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
        if ($form->isSubmitted() && $form->isValid()) {
            $tag = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();
            return $this->redirectToRoute('app_note_tags');
        }

        $tags = $this->getTags($doctrine);

        return $this->renderForm('./new.html.twig', [
            'form_name' => 'タグ編集',
            'tags' => $tags,
            'form' => $form,
        ]);
    }

    /**
     * 表示色の値を正規化する。空文字はnull、#RRGGBB形式以外はnullとして扱う。
     */
    private function resolveDisplayColor(string $value): ?string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $trimmed)) {
            return null;
        }
        return $trimmed;
    }

    /**
     * 表示タイプの値を正規化する。空値は DISPLAY_TYPE_TITLE として扱う（ユーザー入力のデフォルト）。
     * 許可値（title/image）以外の不正値も DISPLAY_TYPE_TITLE に正規化する。
     */
    private function resolveDisplayType(string $value): string
    {
        $trimmed = trim($value);
        if (in_array($trimmed, NoteTags::DISPLAY_TYPE_VALUES, true)) {
            return $trimmed;
        }
        return NoteTags::DISPLAY_TYPE_TITLE;
    }

    /**
     * 親タグIDを検証して返す。
     * - 空文字または"0"はnullを返す
     * - 自分自身のIDを指定した場合はfalseを返す
     * - 存在しないIDを指定した場合はfalseを返す
     * - 有効なIDの場合はintを返す
     *
     * @return int|null|false
     */
    private function resolveParentTagId(string $value, ?int $selfId, ManagerRegistry $doctrine)
    {
        $trimmed = trim($value);
        if ($trimmed === '' || $trimmed === '0') {
            return null;
        }
        if (!ctype_digit($trimmed)) {
            return false;
        }
        $parentId = (int) $trimmed;
        if ($selfId !== null && $parentId === $selfId) {
            return false;
        }
        $exists = $doctrine->getRepository(NoteTags::class)->find($parentId);
        if (!$exists) {
            return false;
        }
        return $parentId;
    }
}

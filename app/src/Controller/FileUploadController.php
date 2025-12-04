<?php

namespace App\Controller;

use App\Entity\NoteTags;

use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\FileUploadType;

class FileUploadController extends BaseController
{
    /**
     * @Route("/upload_files", name="upload_files")
     */
    public function upload_files(Request $request, ManagerRegistry $doctrine): Response
    {

        $form = $this->createForm(FileUploadType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $file1 = $form->get('file1')->getData();
            if($file1)
            {
                $url = $this->uploadFiles($file1);
            }
            $file2 = $form->get('file2')->getData();
            if($file2)
            {
                $url = $this->uploadFiles($file2);
            }
            $file3 = $form->get('file3')->getData();
            if($file3)
            {
                $url = $this->uploadFiles($file3);
            }
            
            return $this->redirectToRoute('show_upload_files');
        }

        $tags = $this->getTags($doctrine);

        return $this->renderForm('./new.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/api/upload_files", name="api_upload_files", methods={"POST"})
     */
    public function api_upload_files(Request $request, ManagerRegistry $doctrine): Response
    {
        $file = $request->files->get('image');
        if (!$file) {
            return new JsonResponse(['error' => 'No image uploaded'], 400);
        }

        $url = $this->uploadFiles($file);

        $baseUrl = $this->getParameter('uploads_base_url');
        $url = str_replace('./uploads/', 'uploads/', $url); // Remove leading './' for URL

        return new JsonResponse([
            'url' => $baseUrl.'/'.$url
        ]);
    }

    private function uploadFiles($file)
    {
        if(!$file) return '';

        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $nowDatetime = date("YmdHis");
        $newFilename = $nowDatetime.'_'.$originalFileName.'.'.$file->guessExtension();

        $upload_dir = './uploads';
        try
        {
            $file->move(
                $upload_dir,
                $newFilename
            );
        }
        catch(FileException $e)
        {
            return 'アップロードに失敗しました';
        }
        return $upload_dir.'/'.$newFilename;
    }

    /**
     * @Route("/upload_files/views", name="show_upload_files")
     */
    public function show_upload_files(Request $request, ManagerRegistry $doctrine): Response
    {
        $filesPerPage = 24;

        $mode = $request->query->get("mode", "");
        $page = $request->query->get("page", 1);
        if($page < 1) $page = 1;

        $baseUrl = $this->getParameter('uploads_base_url');
        $upload_dir = 'uploads';
        $serverFiles = glob($upload_dir.'/202*.*');
        rsort($serverFiles);

        $startIndex = $filesPerPage * ($page - 1);
        $endIndex = $filesPerPage * $page - 1;

        $files = [];
        for($i = $startIndex; $i <= $endIndex && $i < count($serverFiles); $i++){
            $files[] = $baseUrl.'/'.$serverFiles[$i];
        }

        $tags = $this->getTags($doctrine);
        return $this->renderForm('./file_upload/index.html.twig', [
            'form_name' => '',
            'tags' => $tags,
            'page' => $page,
            'files' => $files
        ]);
    }
}

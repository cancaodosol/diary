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

class BaseController extends AbstractController
{
    public function getTags(ManagerRegistry $doctrine)
    {
        $tags = $doctrine->getRepository(NoteTags::class)->findBy([], ["parentTagId" => "ASC"]);
        $parentTags = [];
        foreach ($tags as $tag) {
            if(!$tag->getParentTagId()){
                $parentTags[$tag->getId()] = $tag;
            }
        }
        foreach ($tags as $tag) {
            if($tag->getParentTagId()){
                $parentTags[$tag->getParentTagId()]->appendChildrenTag($tag);
            }
        }
        return $parentTags;
    }
}
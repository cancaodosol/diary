<?php

namespace App\Controller;

use App\Entity\HouseholdAccountRecord;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class HouseholdAccountRecordController extends BaseController
{
    /**
     * @Route("/household/account-records", name="app_household_account_records")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $tags = $this->getTags($doctrine);
        $records = $doctrine->getRepository(HouseholdAccountRecord::class)->findForList();

        return $this->render('household_account_record/index.html.twig', [
            'tags' => $tags,
            'records' => $records,
        ]);
    }
}

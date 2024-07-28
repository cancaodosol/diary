<?php

namespace App\Tests\Helpers;

use DateTime;
use PHPUnit\Framework\TestCase;

use App\Helpers\DateHelper;

class DateHelperTest extends TestCase
{

    public function testGetWeekedndDate(): void
    {
        $helper = new DateHelper();
        $today = new DateTime();
        $d1 = $helper->getWeekedndDate($today);
        print_r($today);
        print_r($d1);
    }

    public function testGetDatesInTheLastWeeks(): void
    {
        $helper = new DateHelper();
        $today = new DateTime();
        $d1 = $helper->getDatesInTheLastWeeks($today, 4);
        print_r($today);
        print_r($d1);
    }
}
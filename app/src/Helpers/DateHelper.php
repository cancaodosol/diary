<?php

namespace App\Helpers;

use App\ValueObject\JapaneseDate;

class DateHelper
{
    public function __construct(){
        $this->weekend_yobi = "日";
    }

    public function getWeekedndDate(\DateTimeInterface $date)
    {
        $weekend = $date;
        for($i = 0; $i <= 7; $i++){
            if($i != 0) $weekend->modify("+1 day");
            $jDate = new JapaneseDate($weekend);
            if($jDate->getYoubi() == $this->weekend_yobi) break;
        }
        return $weekend;
    }

    public function getDatesInTheLastWeeks(\DateTimeInterface $date, int $weeks) : array
    {
        $dates = $weeks * 7;
        $result = [];
        $weekend = $this->getWeekedndDate($date);
        for($i = 0; $i < $dates; $i++){
            if($i != 0) $date->modify("-1 day");
            $result[] = clone $date;
        }
        return $result;
    }

    public function getDatesInTheWeek(\DateTimeInterface $date) : array
    {
        return $this->getDatesInTheWeek($date, 1);
    }
}
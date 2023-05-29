<?php

namespace App\ValueObject;

class JapaneseDate
{
    private $date;

    public function __construct(\DateTimeInterface $date)
    {
        $this->date = $date;
    }

    public function getValue(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function getYoubi(): ?string
    {
        $w_no = $this->date->format('w');
        $week = array('日', '月', '火', '水', '木', '金', '土');
        return $week[$w_no];
    }

    public function toString(): ?string
    {
        # PHPのDateフォーマット：
        # https://www.flatflag.nir87.com/date-473#date
        $youbi = $this->getYoubi(); 
        $strDate = $this->date->format('Y年n月j日');
        return $strDate . '(' . $youbi . ')';
    }
}
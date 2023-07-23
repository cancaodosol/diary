<?php

namespace App\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;

use App\Entity\UnitaryNote;
use App\Entity\Diary;

class UnitaryNoteTest extends TestCase
{
    public function testSplit(): void
    {
        $diary = new Diary();
        $date = new DateTime('2023-06-24 03:04:05');
        $diary->setDate($date);
        $text = '<strong>13:20 - 13:21　テスト。行動記録未記入の時の動き。</strong>

        ここにはここに。
        
        <hr>
        <strong>13:23 - 13:23　もう一度</strong>
        
        ここにはここに。
        
        <hr>
        <strong>13:23 - 13:24　普通に書いても？</strong>
        
        いけていますか？
        
        こちらではどうでしょうか？
        
        <hr>
        <strong>00:29 - 00:40　タイトルだけ書いて保存したい</strong>
        
        
        <hr>
        <strong>02:59 - 03:59　これから、Divが記載されることになった</strong>
        
        全くもって、問題だ。';

        $diary->setText($text);

        $units = explode('<hr>', $diary->getText());

        $notes = [];
        foreach($units as $unit)
        {
            $pattern = '@<strong>(.*)</strong>@';
            $title = '';
            $text = '';
            if( preg_match_all($pattern, $unit, $result) ){
                $title_html = $result[0][0];
                $title = $result[1][0];
                $nowHour = (int)substr($title, 0, 2);
                $text = str_replace($title_html, '', $unit);
            }
            $text = trim($text);
            $note = New UnitaryNote();
            $note->setDate($diary->getDate());
            $note->setTitle($title);
            $note->setText($text);
            $notes[] = $note;
        }

        $preHour = 0;
        $plus24hours = false;
        foreach($notes as $note){
            $title = $note->getTitle();
            $nowHour = (int)substr($title, 0, 2);
            if($plus24hours == false && $nowHour < $preHour) $plus24hours = true;
            if($plus24hours == true){
                $title = str_replace(substr($title, 0, 3), sprintf("%'.02d:", $nowHour + 24), $title);
            }
            if($title != $note->getTitle()) $note->setTitle($title);
            $preHour = $nowHour;
        }

        $this->assertTrue(count($notes) == 5);
        $this->assertTrue($notes[0]->getTitle() == '13:20 - 13:21　テスト。行動記録未記入の時の動き。');
        $this->assertTrue($notes[3]->getTitle() == '24:29 - 24:40　タイトルだけ書いて保存したい');
        $this->assertTrue($notes[4]->getTitle() == '26:59 - 03:59　これから、Divが記載されることになった');
        $this->assertTrue($notes[0]->getText() == 'ここにはここに。');
    }
}

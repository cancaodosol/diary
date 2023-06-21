<?php

namespace App\ValueObject;

class HtmlText
{
    private $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function getTextHtml(): ?string
    {
        $textHtml = $this->text;

        # 改行コードの前に、<br />を追記。
        $textHtml = nl2br($textHtml);

        # TODO [code][/code]内の<br />を除去

        # [code][/code]の置換
        $textHtml = str_replace('[code]', '<div class="code-text" style="font-size:0.8rem;"><script type="text/plain" class="language-php line-numbers">', $textHtml);
        $textHtml = str_replace('[/code]', '</script></div>', $textHtml);

        # [引用][/引用]の置換
        $textHtml = str_replace('[引用]', '<div class="box"><p>', $textHtml);
        $textHtml = str_replace('[/引用]', '</p></div>', $textHtml);

        # Youtubu埋め込みを横幅100％対応
        $youtubeIframStart = '<iframe width="560" height="315" src="https://www.youtube.com';
        $youtubeIframEnd = 'allowfullscreen></iframe>';
        $textHtml = str_replace($youtubeIframStart, '<div class="youtube-wrap"><div class="youtube">'.$youtubeIframStart, $textHtml);
        $textHtml = str_replace($youtubeIframEnd, $youtubeIframEnd.'</div></div>', $textHtml);

        return $textHtml;
    }
}
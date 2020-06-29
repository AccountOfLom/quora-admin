<?php

namespace App\Console\Commends;


use App\Models\Answers;
use App\Models\Questions;
use App\Server\Translate\Youdao;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * 翻译回答
 * Class TranslateYoudao
 * @package App\Console\Commends
 */
class TranslateYoudao extends Command
{

    protected $signature = 'translate-youdao';


    protected $description = 'translate youdao';

    protected $elements = ['p', 'span', 'li', 'ol', 'a']; //文本内容标签

    protected $reElements = [
        [
            'e'     => '<b>',
            'cn'    => '标签一开始'
        ],
        [
            'e'     => '</b>',
            'cn'    => '标签一结束'
        ],
        [
            'e'     => '<i>',
            'cn'    => '标签二开始'
        ],
        [
            'e'     => '</i>',
            'cn'    => '标签二结束'
        ]
    ];


    public function handle()
    {
        try {
            $answer = Answers::where('image_fetched', 1)->whereNull('content_youdao')->orderBy('id', 'desc')->first();
            if (!$answer) {
                return false;
            }
            $content = $answer->content;

            $client = new Youdao();
            foreach ($this->elements as $element) {
                preg_match_all("/<$element.*?>(.*?)(?=<\/$element>)/im", $content, $matches);
                if (count($matches[0]) == 0) {
                    continue;
                }

                foreach ($matches[1] as $index => $match) {
                    $reElementContent = $match;
                    foreach ($this->reElements as $element) {
                        $reElementContent = str_replace($element['e'], $element['cn'], $reElementContent);  //替换在翻译后会被忽略的标签
                    }

                    $contentCN = $client->translate($reElementContent);

                    foreach ($this->reElements as $element) {
                        $contentCN = str_replace($element['cn'], $element['e'], $contentCN);
                    }

                    $content = str_replace($match, $contentCN, $content);
                }

            }
            $content = str_replace('< ', '<', $content);   //去空格
            $content = str_replace(' >', '>', $content);
            $content = str_replace('</ ', '</', $content);
            $content = str_replace(' />', '/>', $content);

            if ($content) {
                Answers::where('id', $answer->id)->update(['content_youdao' => $content]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            echo 'translate youdao ' . $e->getMessage();
        }
        echo 'translate youdao success';
    }
}

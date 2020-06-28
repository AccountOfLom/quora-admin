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


    public function handle()
    {
//        try {
            $client = new Youdao();
//        $answer = Answers::whereNull('content_youdao')->orderBy('id', 'desc')->first();
        $answer = Answers::where('id', 1114)->orderBy('id', 'desc')->first();
            if (!$answer) {
                return false;
            }
            foreach ($this->elements as $element) {
                preg_match_all("/<$element.*?>(.*?)(?=<\/$element>)/im", $answer->content, $matches);

            }
        preg_match_all('/<p.*?>(.*?)(?=<\/p>)/im', $answer->content, $matches);
        echo '<pre>';
        print_r($matches);
        die;
            $content = '';

            if ($content) {
                Answers::where('id', $answer->id)->update(['content_youdao' => $content]);
            }
//        } catch (\Exception $e) {
//            Log::error($e->getMessage());
//            echo 'translate youdao ' . $e->getMessage();
//        }
        echo 'translate youdao success';
    }
}

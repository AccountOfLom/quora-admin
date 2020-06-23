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


    public function handle()
    {
        try {
            set_time_limit(0);
            $client = new Youdao();
            $answer = Answers::where(['translated' => 0, 'command' => 0])->first();
            if (!$answer) {
                return false;
            }
            $question = Questions::where('id', $answer->question_id)->first();
            if (!$question->text_cn) {
                $textCN = $client->translate($question->text);
                Questions::where('id', $answer->question_id)->update(['text_cn' => $textCN]);
                sleep(2);  //API调用频率限制
            }

            if (!$answer->user_name_cn) {
                $userNameCN = $client->translate($answer->user_name);
                Answers::where('id', $answer->id)->update(['user_name_cn' => $userNameCN]);
                sleep(2);
            }

            if ($answer->user_credential && !$answer->user_credential_cn) {
                if ($answer->user_credential[0] == ',') {   //去掉前面的　, 号
                    $answer->user_credential = substr($answer->user_credential, 1, strlen($answer->user_credential));
                }
                $userCredentialCN = $client->translate($answer->user_credential);
                Answers::where('id', $answer->id)->update([
                    'user_credential' => $answer->user_credential,
                    'user_credential_cn' => $userCredentialCN
                ]);
                sleep(2);
            }

            if ($answer->user_info && !$answer->user_info_cn) {
                $userInfoCN = $client->translate($answer->user_info);
                Answers::where('id', $answer->id)->update(['user_info_cn' => $userInfoCN]);
                sleep(2);
                return false;   //　答案内容下次执行时再翻译
            }

            if (strpos($answer->content, 'class') !== false) {
                $answer->content = preg_replace('/class="(.*?)"/', '', $answer->content);
                Answers::where('id', $answer->id)->update(['content' => $answer->content]);
            }
            Answers::where('id', $answer->id)->update(['command' => 1]); //标记为已执行翻译任务
            $translateResult = '';
            $j = 2000;  //API翻译长度限制  2000个汉字
            $i = 0;
            while (true) {
                $strpos = substr($answer->content, $i, $j);
                if (!$strpos) {
                    break;
                }
                if (strlen($strpos) < $j) {
                    $translateResult .= $client->translate($strpos);   //不足　2000 长度，无需剪切，直接翻译
                    break;
                }
                $posIndex = strripos($strpos, '</');  //找最后一个闭合标签
                $content = substr($answer->content, $i, $posIndex);
                if (empty($content)) {  //所查字符长度内无闭合标签
                    $posIndex = strripos($strpos, '<');  //找最后一个开始标签
                    $content = substr($answer->content, $i, $posIndex);
                }
                $translateResult .= $client->translate($content);
                $i += $posIndex;
                sleep(1);
            }
            if ($translateResult) {
                $translateResult = str_replace('”', '"', $translateResult);   //引号转回英文标点,不然html会无法解析
                $translateResult = str_replace('“', '" ', $translateResult);
                Answers::where('id', $answer->id)->update(['translated' => 1, 'content_cn' => $translateResult]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            echo 'translate youdao ' . $e->getMessage();
        }
        echo 'translate youdao success';
    }
}

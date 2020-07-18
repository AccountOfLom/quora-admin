<?php


namespace App\Console\Commends;


use App\Models\Answers;
use App\Models\Answers as AnswersModel;
use App\Server\Qiniu;
use Illuminate\Console\Command;

/**
 * 抓取图片到七牛云
 * Class ImageFetched
 * @package App\Console\Commends
 */
class ImageFetched extends Command
{

    protected $signature = 'image-fetched';


    protected $description = 'image fetched';


    public function handle()
    {
        $answer = Answers::where('image_fetched', 0)->orderBy('id', 'desc')->first();
        $content = $answer->content;
        $content = preg_replace('/class="(.*?)"/', '', $content); //删除class
        $content = preg_replace('/style="(.*?)"/', '', $content); //删除style
        $content = preg_replace('/<a .*?a>/is', '', $content); //删除a链接
        $content = str_replace(['<p >', '< p>', '<p  >', '<P   >'], '<p>', $content);
        $content = str_replace(['<span >', '< span>', '<span  >', '<span   >'], '<span>', $content);
        $content = str_replace(['<div >', '< div>', '<div  >', '<div   >'], '<div>', $content);
        $content = str_replace(['<ol >', '< ol>', '<ol  >', '<ol   >'], '<ol>', $content);
        $content = str_replace(['<ul >', '< ul>', '<ul  >', '<ul   >'], '<ul>', $content);
        $content = str_replace([' />', '  />'], '/>', $content);
        $answer->content = $content;
        $qiniu = new Qiniu();
        if (strpos($answer->user_avatar, env('QINIU_DOMAIN')) === false) {
            $answer->user_avatar = $qiniu->fetch($answer->user_avatar);
        }
        $preg =  '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
        preg_match_all($preg, $answer->content, $images);
        if (!empty($images[0])) {
            foreach ($images[1] as $index => $item) {
                if (strpos($item, env('QINIU_DOMAIN')) !== false) {
                    continue;
                }
                $image = $qiniu->fetch($item);
                $answer->content = str_replace($images[0][$index], '<img src="' . $image . '" />', $answer->content);
            }
        }

        $answer->image_fetched = 1;
        $answer->save();
        echo 'image fetched success';
    }
}

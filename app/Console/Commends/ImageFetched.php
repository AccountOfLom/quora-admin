<?php


namespace App\Console\Commends;


use App\Models\Answers;
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
        if (!$answer) {
            return true;
        }
        $qiniu = new Qiniu();
        $answer->user_avatar = $qiniu->fetch($answer->user_avatar);
        $preg =  '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
        preg_match_all($preg, $answer->content, $images);
        if (!empty($images[0])) {
            foreach ($images[1] as $index => $item) {
                $image = $qiniu->fetch($item);
                $answer->content = str_replace($images[0][$index], '<img src="' . $image . '" />', $answer->content);
            }
        }
        $answer->image_fetched = 1;
        $answer->save();
        echo 'image fetched success';
    }
}

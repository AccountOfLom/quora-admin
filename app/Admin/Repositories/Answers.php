<?php


namespace App\Admin\Repositories;


use App\Server\Qiniu;
use Dcat\Admin\Repositories\EloquentRepository;
use App\Models\Answers as AnswersModel;

class Answers extends EloquentRepository
{
    protected $eloquentClass = AnswersModel::class;

    public function info($questionID)
    {
        return AnswersModel::where(['question_id' => $questionID, 'translated' => 1])->get()->toArray();
    }

    public function replaceImgElement($id)
    {
        $answer = AnswersModel::where('id', $id)->first();
        if ($answer->image_fetched == 1) {
            return true;
        }
        $qiniu = new Qiniu();
        $answer->user_avatar = $qiniu->fetch($answer->user_avatar);
        $preg =  '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
        preg_match_all($preg, $answer->content, $images);
        if (!empty($images[0])) {
            foreach ($images[1] as $index => $item) {
                if (strpos(env('QINIU_DOMAIN'), $item) !== false) {
                    continue;
                }
                $image = $qiniu->fetch($item);
                $answer->content = str_replace($images[0][$index], '<img src="' . $image . '" />', $answer->content);
            }
        }
        if ($answer->content_cn) {
            preg_match_all($preg, $answer->content_cn, $images);
            if (!empty($images[0])) {
                foreach ($images[1] as $index => $item) {
                    if (strpos(env('QINIU_DOMAIN'), $item) !== false) {
                        continue;
                    }
                    $image = $qiniu->fetch($item);
                    $answer->content_cn = str_replace($item, $image, $answer->content_cn);
                }
            }
        }

        $answer->image_fetched = 1;
        $answer->save();
        return true;
    }

    public function saveWxHtml($questionID, $html)
    {
        if (!$html) {
            return false;
        }
        return AnswersModel::where(['id' => $questionID])->update(['wx_html' => $html]);
    }
}

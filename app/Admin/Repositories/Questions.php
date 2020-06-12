<?php


namespace App\Admin\Repositories;


use Dcat\Admin\Repositories\EloquentRepository;
use App\Models\Questions as QuestionsModel;

class Questions extends EloquentRepository
{
    protected $eloquentClass = QuestionsModel::class;


    public function getWxHtml($questionID)
    {
        return QuestionsModel::where('id', $questionID)->value('wx_html');
    }

    public function saveWxHtml($questionID, $html)
    {
        if (!$html) {
            return false;
        }
        return QuestionsModel::where('id', $questionID)->update(['wx_html' => $html]);
    }

}

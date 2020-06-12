<?php


namespace App\Admin\Repositories;


use Dcat\Admin\Repositories\EloquentRepository;
use App\Models\Answers as AnswersModel;

class Answers extends EloquentRepository
{
    protected $eloquentClass = AnswersModel::class;

    public function info($questionID)
    {
        return AnswersModel::where(['question_id' => $questionID, 'translated' => 1])->get()->toArray();
    }
}

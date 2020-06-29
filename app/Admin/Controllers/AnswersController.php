<?php

namespace App\Admin\Controllers;


use App\Admin\Repositories\Answers;
use App\Models\Questions;
use App\Server\AnswersHtml;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Illuminate\Support\Facades\Request;


/**
 * Class AnswersController
 * @package App\Admin\Controllers
 */
class AnswersController extends BaseController
{

    public function index(Content $content)
    {
        return $content
            ->header('Answers')
            ->body($this->grid());
    }

    public function wxHtml($questionID, Content $content)
    {
        $enContent = AnswersHtml::en($questionID);
        $baiduContent = AnswersHtml::wx($questionID, 'baidu');
        $youdaoContent = AnswersHtml::wx($questionID, 'youdao');
        $question = Questions::where('id', $questionID)->first();
        return $content
            ->header('回答集合')
            ->body(view('admin.answers.wx-html', [
                'title' => $question->text_cn,
                'baiduContent' => $baiduContent,
                'youdaoContent' => $youdaoContent,
                'enContent' => $enContent
            ]));
    }

    protected function grid()
    {

    }

    public function edit($id, Content $content)
    {
    }

    public function create(Content $content)
    {
    }

    public function show($id, Content $content)
    {

    }

    protected function form()
    {

    }
}

<?php

namespace App\Admin\Controllers;


use App\Admin\Repositories\Answers;
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
        $wxHtml = AnswersHtml::wx($questionID);
        $enHtml = AnswersHtml::en($questionID);
        return $content
            ->header('Dashboard')
            ->description('Description...')
            ->body(view('admin.answers.wx-html', ['wxHtml' => $wxHtml, 'enHtml' => $enHtml]));
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

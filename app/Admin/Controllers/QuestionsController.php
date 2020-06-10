<?php


namespace App\Admin\Controllers;

use App\Admin\Repositories\Questions;
use App\Models\Answers;
use App\Models\Topics;
use App\Models\Questions as QuestionsModel;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Illuminate\Support\Facades\DB;


/**
 * 问题
 * Class QuestionsController
 * @package App\Admin\Controllers
 */
class QuestionsController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('问题')
            ->body($this->grid());
    }

    protected function grid()
    {
        return Grid::make(new Questions(), function (Grid $grid) {
            $grid->disableCreateButton();
            $grid->paginate(10);
            $grid->quickSearch(['ID', 'text', 'text_cn']);
            $grid->filter(function($filter){
                $filter->equal('id', 'ID');
                $filter->like('topic', '话题 CN');
                $filter->like('text_cn', '问题 CN');
                $filter->like('text', '问题 en');
                $filter->date('created_at', '爬取日期');
                $filter->equal('article_released', '公众号')->select([0 => '未发布', 1 => '已发布']);
                $filter->equal('revised', '修改润色')->select([0 => '已润色', 1 => '未润色']);
                $filter->equal('status', '显示状态')->select([0 => '隐藏', 1 => '显示']);
            });
            $grid->model()->orderBy('id', 'desc');
            $grid->id('ID')->sortable();
            $grid->topic('话题')->display(function ($topic) {
                return Topics::where('topic', $topic)->value('topic_cn');
            })->width('80px');
            $grid->text('问题')->width('360px');
            $grid->text_cn('问题 CN')->display(function ($text_cn) {
                return $text_cn ? $text_cn : '点击编辑';
            })->editable(true)->width('360px');
            $grid->link('Quora')->display(function ($link) {
                return "<a class='text-info' href='https://www.quora.com{$link}' target='_blank'>前往Quora</a>";
            });
            $grid->column('回答')->display(function () {
                $count = Answers::where('question_id', $this->id)->count();
                $translatedCount = Answers::where(['question_id' => $this->id, 'translated' => 1])->count();
                return '<p>回答数：<span class="text-primary">'. $count .'</span></p>
                        <p>已翻译：<span class="text-success">'. $translatedCount .'</span></p>';
            })->width('100px');
            $grid->switch_group->switchGroup(['status', 'article_released', 'revised'])->width('150px');
            $grid->created_at;
        });
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('问题')
            ->body($this->form()->edit($id));
    }

    public function destroy($ids)
    {
        $ids = explode(',', $ids);
        DB::beginTransaction();
        try {
            QuestionsModel::destroy($ids);
            Answers::whereIn('question_id', $ids)->delete();
            $data = [
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $data = [
                'status'  => false,
                'message' => trans($e->getMessage()),
            ];
        }
        return response()->json($data);
    }



    public function show($id, Content $content)
    {
        return $content->header('问题')
            ->body(Show::make($id, new Questions(), function (Show $show) {
                $show->id('ID');
                $show->topic('话题');
                $show->text('问题');
                $show->text_cn('问题 CN');
                $show->link('Quora 地址');
                $show->statue('显示状态');
                $show->article_released('发布到公众号');
                $show->revised('修改润色');
                $show->created_at();
                $show->updated_at();
            }));
    }

    protected function form()
    {
        $form = Form::make(new Questions(), function (Form $form) {
            $form->display('id', 'ID');
            $form->text('topic', '话题');
            $form->text('text', '问题');
            $form->text('text_cn', '问题 CN');
            $form->text('link', 'Quora 地址');
            $form->switch('status', '显示状态');
            $form->switch('article_released', '发布到公众号');
            $form->switch('revised', '修改润色');
        });
        return $form;
    }
}
